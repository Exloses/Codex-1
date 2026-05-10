<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\SupportReplyRequest;
use App\Http\Requests\Storefront\SupportTicketRequest;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class SupportTicketController extends Controller
{
    public function store(SupportTicketRequest $request)
    {
        $ticket = SupportTicket::query()->create(array_merge($request->safe()->only(['subject', 'message', 'priority', 'order_id']), [
            'user_id' => $request->user()->id,
            'ticket_number' => 'TCK-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
            'status' => 'open',
            'priority' => $request->input('priority', 'normal'),
        ]));

        return $this->ok(['ticket' => $ticket], 201);
    }

    public function show(SupportTicket $ticket): Response
    {
        $this->authorize('view', $ticket);

        return Inertia::render('Account/SupportTickets', ['ticket' => $ticket->load('replies.user')]);
    }

    public function reply(SupportReplyRequest $request, SupportTicket $ticket)
    {
        $this->authorize('view', $ticket);

        $reply = TicketReply::query()->create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'message' => $request->validated('message'),
            'is_staff' => $request->user()->isAdmin(),
        ]);

        return $this->ok(['reply' => $reply], 201);
    }
}
