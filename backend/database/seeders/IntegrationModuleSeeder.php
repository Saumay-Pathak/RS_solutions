<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IntegrationModule;

class IntegrationModuleSeeder extends Seeder
{
    /**
     * Seed demo Integration Modules data.
     */
    public function run(): void
    {
        // Stripe Payments Integration
        IntegrationModule::create([
            'title' => 'Stripe Payments',
            'description' => 'Accept online payments with Stripe. Supports subscriptions, invoices, and one-time charges.',
            'key_features' => [
                'One-time and recurring payments',
                'Secure card vault and tokenization',
                'Webhook-based event handling',
            ],
            'api_features' => [
                'Payment Intents API',
                'Subscriptions & Invoices API',
                'Webhook signature verification',
            ],
            'api_documentations' => [
                ['title' => 'Payment Intents', 'url' => 'https://stripe.com/docs/payments/payment-intents'],
                ['title' => 'Subscriptions', 'url' => 'https://stripe.com/docs/billing/subscriptions/overview'],
                ['title' => 'Webhook Signatures', 'url' => 'https://stripe.com/docs/webhooks/signatures'],
            ],
            'production_base_url' => 'https://api.stripe.com',
            'staging_base_url' => 'https://api.stripe.com',
            'demo_credentials' => [
                'username' => 'demo@stripe.local',
                'password' => 'demo_password',
                'notes' => 'Use Stripe test cards: 4242 4242 4242 4242',
            ],
            'apis' => [
                [
                    'name' => 'Payment Intents',
                    'type' => 'REST',
                    'method' => 'POST',
                    'base_url' => 'https://api.stripe.com/v1/payment_intents',
                    'description' => 'Create and confirm payments',
                    'headers' => [
                        'Authorization' => 'Bearer sk_test_xxx',
                        'Content-Type' => 'application/x-www-form-urlencoded'
                    ],
                    'body' => [
                        'amount' => 1000,
                        'currency' => 'usd',
                        'automatic_payment_methods' => ['enabled' => true]
                    ]
                ],
                [
                    'name' => 'Webhooks',
                    'type' => 'Events',
                    'method' => 'POST',
                    'base_url' => 'https://yourapp.example.com/webhooks/stripe',
                    'description' => 'Receive events for payment updates',
                    'headers' => [
                        'Stripe-Signature' => 't=timestamp,v1=signature'
                    ],
                    'body' => [
                        'id' => 'evt_123',
                        'type' => 'payment_intent.succeeded',
                        'data' => ['object' => ['id' => 'pi_123']]
                    ]
                ],
            ],
            'services_api' => [
                'Payments processing',
                'Subscriptions billing',
                'Refunds and disputes',
            ],
            'services_other' => [
                'PCI compliance guidance',
                'Fraud prevention tooling',
            ],
            'status' => true,
            'sort_order' => 10,
            'meta_title' => 'Stripe Payments Integration',
            'meta_description' => 'Stripe integration for secure payments, subscriptions and webhooks.',
            'meta_keywords' => 'stripe, payments, subscriptions, webhooks',
        ]);

        // Slack Notifications Integration
        IntegrationModule::create([
            'title' => 'Slack Notifications',
            'description' => 'Send automated notifications to Slack channels and users with rich formatting.',
            'key_features' => [
                'Channel and DM messages',
                'Slash commands and interactions',
                'Blocks UI formatting',
            ],
            'api_features' => [
                'Web API for messaging',
                'Events API for subscriptions',
                'OAuth 2.0 app installation',
            ],
            'api_documentations' => [
                ['title' => 'Web API', 'url' => 'https://api.slack.com/web'],
                ['title' => 'Events API', 'url' => 'https://api.slack.com/apis/connections/events-api'],
                ['title' => 'OAuth', 'url' => 'https://api.slack.com/authentication/oauth-v2'],
            ],
            'production_base_url' => 'https://slack.com/api',
            'staging_base_url' => 'https://slack.com/api',
            'demo_credentials' => [
                'username' => 'bot@slack.local',
                'password' => 'demo_password',
                'notes' => 'Use test workspace and bot token xoxb-***',
            ],
            'apis' => [
                [
                    'name' => 'chat.postMessage',
                    'type' => 'REST',
                    'method' => 'POST',
                    'base_url' => 'https://slack.com/api/chat.postMessage',
                    'description' => 'Post messages to channels',
                    'headers' => [
                        'Authorization' => 'Bearer xoxb-xxx',
                        'Content-Type' => 'application/json'
                    ],
                    'body' => [
                        'channel' => '#general',
                        'text' => 'Hello from demo seeder!',
                        'mrkdwn' => true
                    ]
                ],
                [
                    'name' => 'Events',
                    'type' => 'Events',
                    'method' => 'POST',
                    'base_url' => 'https://yourapp.example.com/webhooks/slack/events',
                    'description' => 'Receive callbacks for subscribed events',
                    'headers' => [
                        'X-Slack-Signature' => 'v0=signature',
                        'X-Slack-Request-Timestamp' => 'timestamp'
                    ],
                    'body' => [
                        'type' => 'event_callback',
                        'event' => ['type' => 'message', 'channel' => 'C123', 'text' => 'Hi']
                    ]
                ],
            ],
            'services_api' => [
                'Notifications delivery',
                'Interactive buttons and menus',
                'Slash commands processing',
            ],
            'services_other' => [
                'App installation workflows',
                'Security and token management',
            ],
            'status' => true,
            'sort_order' => 20,
            'meta_title' => 'Slack Notifications Integration',
            'meta_description' => 'Slack integration for automated messaging and events.',
            'meta_keywords' => 'slack, notifications, events, oauth',
        ]);
    }
}
