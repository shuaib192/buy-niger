<?php
/**
 * BuyNiger AI - Multi-Vendor E-Commerce Platform
 * Written by Shuaibu Abdulmumin (08122598372, 07049906420)
 * 
 * Listener: SendCampaignEmails
 */

namespace App\Listeners;

use App\Events\CampaignLaunched;
use App\Jobs\SendEmailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class SendCampaignEmails implements ShouldQueue
{
    public function handle(CampaignLaunched $event): void
    {
        $campaign = DB::table('email_campaigns')->find($event->campaignId);
        
        if (!$campaign) {
            return;
        }

        // Get target recipients based on audience
        $recipients = $this->getRecipients($campaign);

        // Update campaign status
        DB::table('email_campaigns')
            ->where('id', $event->campaignId)
            ->update([
                'status' => 'sending',
                'total_recipients' => count($recipients),
                'updated_at' => now(),
            ]);

        // Queue emails for each recipient
        foreach ($recipients as $recipient) {
            SendEmailNotification::dispatch(
                $recipient->email,
                $recipient->name,
                'campaign_' . $event->campaignId,
                [
                    'customer_name' => $recipient->name,
                    'campaign_content' => $campaign->body,
                ],
                $recipient->id,
                $event->campaignId
            );
        }

        // Update campaign as sent
        DB::table('email_campaigns')
            ->where('id', $event->campaignId)
            ->update([
                'status' => 'sent',
                'sent_at' => now(),
                'updated_at' => now(),
            ]);
    }

    protected function getRecipients($campaign): array
    {
        $query = DB::table('users')->where('is_active', true);

        switch ($campaign->target_audience) {
            case 'customers':
                $query->where('role_id', 4);
                break;
            case 'vendors':
                $query->where('role_id', 3);
                break;
            case 'custom':
                // Apply custom filters from target_filters JSON
                $filters = json_decode($campaign->target_filters, true) ?? [];
                // Apply filters as needed
                break;
            case 'all':
            default:
                $query->whereIn('role_id', [3, 4]); // Vendors and customers
                break;
        }

        return $query->get()->all();
    }
}
