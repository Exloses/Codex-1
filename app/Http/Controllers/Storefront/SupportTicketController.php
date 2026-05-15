<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\SupportReplyRequest;
use App\Http\Requests\Storefront\SupportTicketRequest;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Models\User;
use App\Notifications\SupportTicketCreatedNotification;
use App\Notifications\SupportTicketReplyNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SupportTicketController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Account/Support/Index', [
            'tickets' => $request->user()->supportTickets()
                ->with('order:id,order_number')
                ->withCount('replies')
                ->latest()
                ->paginate(10)
                ->through(fn (SupportTicket $ticket) => [
                    'id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'subject' => $ticket->subject,
                    'status' => $ticket->status,
                    'priority' => $ticket->priority,
                    'created_at' => $ticket->created_at,
                    'updated_at' => $ticket->updated_at,
                    'replies_count' => $ticket->replies_count,
                    'order' => $ticket->order ? [
                        'id' => $ticket->order->id,
                        'order_number' => $ticket->order->order_number,
                    ] : null,
                ]),
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Account/Support/Create', [
            'orders' => $request->user()->orders()
                ->select(['id', 'order_number', 'created_at'])
                ->latest()
                ->limit(20)
                ->get(),
            'priorities' => SupportTicket::priorities(),
        ]);
    }

    public function store(SupportTicketRequest $request): RedirectResponse
    {
        $ticket = SupportTicket::query()->create(array_merge($request->safe()->only(['subject', 'message', 'priority', 'order_id']), [
            'user_id' => $request->user()->id,
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'status' => SupportTicket::STATUS_OPEN,
            'priority' => $request->input('priority', SupportTicket::PRIORITY_NORMAL),
        ]));

        $request->user()->notify(new SupportTicketCreatedNotification($ticket));
        $this->notifyAdmins(new SupportTicketCreatedNotification($ticket, [
            'action_url' => url('/admin/support-tickets/'.$ticket->id.'/edit'),
        ]));

        return redirect()
            ->route('support.show', $ticket)
            ->with('status', 'Support ticket opened.');
    }

    public function show(SupportTicket $ticket): Response
    {
        $this->authorize('view', $ticket);

        return Inertia::render('Account/Support/Show', [
            'ticket' => $ticket->load([
                'order:id,order_number',
                'replies' => fn ($query) => $query->with('user:id,name,email')->oldest(),
            ]),
        ]);
    }

    public function reply(SupportReplyRequest $request, SupportTicket $ticket): RedirectResponse
    {
        $this->authorize('view', $ticket);

        $reply = TicketReply::query()->create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'message' => $request->validated('message'),
            'is_staff' => $request->user()->isAdmin(),
        ]);

        $ticket->update([
            'status' => $request->user()->isAdmin()
                ? SupportTicket::STATUS_PENDING_CUSTOMER
                : SupportTicket::STATUS_OPEN,
        ]);

        if ($request->user()->isAdmin() && $ticket->user) {
            $ticket->user->notify(new SupportTicketReplyNotification($ticket, $reply->load('user')));
        } else {
            $this->notifyAdmins(new SupportTicketReplyNotification($ticket, $reply->load('user'), [
                'sender_name' => $request->user()->name,
                'action_url' => url('/admin/support-tickets/'.$ticket->id.'/edit'),
            ]));
        }

        return back()->with('status', 'Reply sent.');
    }

    private function notifyAdmins(object $notification): void
    {
        User::query()
            ->where('is_active', true)
            ->whereHas('roles', fn ($query) => $query->where('name', 'admin'))
            ->each(fn (User $admin) => $admin->notify($notification));
    }
}
