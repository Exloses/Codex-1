<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            'shipping' => [
                ['How long does international shipping take?', 'Standard shipping usually takes 7-21 business days depending on the destination zone. Express shipping is faster and shown at checkout.'],
                ['Can I track my order?', 'Yes. Use the Track Order page with your order number and email, or open the order detail from your account.'],
                ['Which countries do you ship to?', 'We currently seed demo zones for Southeast Asia, East Asia, Australia, the Americas, and Europe. Availability can vary by product weight and carrier.'],
                ['Do vendors ship directly to customers?', 'Vendors prepare dropship orders, then GlobalDropship coordinates tracking and customer updates through the platform.'],
            ],
            'payment' => [
                ['Which payment methods are supported?', 'The platform is prepared for card payments through Stripe and PayPal checkout flows, with local testing using safe placeholder credentials.'],
                ['When is my card charged?', 'Payment is captured during checkout before vendors begin fulfillment. Failed payments leave the order unpaid.'],
                ['Can I pay in my local currency?', 'Prices are stored in USD and can be displayed in supported currencies through the storefront currency selector.'],
                ['Are payment details stored by GlobalDropship?', 'No. Sensitive card or wallet details are handled by the payment provider and are not stored in the application database.'],
            ],
            'returns' => [
                ['How do I request a return?', 'Open your account order detail and submit a return request with the reason, description, and supporting images when needed.'],
                ['How long does return review take?', 'Most return requests are reviewed within 2-3 business days after support receives complete information.'],
                ['How are refunds processed?', 'Approved refunds are recorded with the selected refund method and processed by the support or finance team.'],
                ['Can I return customized items?', 'Customized or personalized items may have limited return eligibility unless they arrive damaged or incorrect.'],
            ],
            'account' => [
                ['Do I need an account to buy?', 'Registered accounts can manage orders, addresses, wishlist, notifications, support tickets, and loyalty points.'],
                ['How do I become a vendor?', 'Create an account and submit the vendor application. Admin approval is required before products can be sold.'],
                ['How do affiliate accounts work?', 'Affiliates receive a referral code and link, then earn commission from eligible referred orders.'],
                ['Can I change my preferred language or currency?', 'Yes. Use the storefront selectors to update your session preferences.'],
            ],
            'products' => [
                ['Are products stocked by GlobalDropship?', 'Products are supplied by approved Indonesian vendors and listed through the GlobalDropship platform.'],
                ['How do variants work?', 'Products can include attributes such as color and size. Choose a variant before adding an item to cart.'],
                ['Why do prices change?', 'Prices can change because of vendor updates, promotions, currency display rates, or stock availability.'],
                ['Can I ask questions before buying?', 'Yes. Product Q&A lets buyers ask questions that can be answered by vendors or verified staff.'],
            ],
        ];

        $sortOrder = 1;

        foreach ($faqs as $category => $items) {
            foreach ($items as [$question, $answer]) {
                Faq::updateOrCreate(
                    ['category' => $category, 'question' => $question, 'language' => 'en'],
                    [
                        'answer' => $answer,
                        'sort_order' => $sortOrder++,
                        'helpful_count' => 0,
                        'is_active' => true,
                    ],
                );
            }
        }
    }
}
