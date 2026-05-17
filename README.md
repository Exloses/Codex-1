<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Demo Credentials

- Admin: `admin@platform.com` / `Admin123!`
- Vendor: `vendor1@demo.com` / `Vendor123!`
- Vendor: `vendor2@demo.com` / `Vendor123!`
- Buyer: `buyer@demo.com` / `Buyer123!`

## Local Social Login Setup

Task 16 adds Google and Facebook social login through Laravel Socialite. The repository only stores safe placeholders in `.env.example`; real OAuth client IDs and secrets must stay in your local `.env` file and must never be committed.

For Google OAuth local testing, create an OAuth client in Google Cloud Console and add this authorized redirect URI:

```text
http://localhost:8000/auth/google/callback
```

Then set these values in local `.env`:

```env
GOOGLE_CLIENT_ID=YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=YOUR_GOOGLE_CLIENT_SECRET
GOOGLE_REDIRECT_URL=http://localhost:8000/auth/google/callback
```

For Facebook OAuth local testing, create an app in Meta for Developers and add this redirect URI:

```text
http://localhost:8000/auth/facebook/callback
```

Then set these values in local `.env`:

```env
FACEBOOK_CLIENT_ID=YOUR_FACEBOOK_APP_ID
FACEBOOK_CLIENT_SECRET=YOUR_FACEBOOK_CLIENT_SECRET
FACEBOOK_REDIRECT_URL=http://localhost:8000/auth/facebook/callback
```

## Live Chat and Support

Task 18 adds the account support ticket workflow and optional Tawk.to live chat. Tawk.to stays disabled unless both values are present and not placeholders, and it is also disabled during automated tests.

Use safe placeholders in committed examples only:

```env
VITE_TAWK_PROPERTY_ID=YOUR_TAWK_PROPERTY_ID
VITE_TAWK_WIDGET_ID=YOUR_TAWK_WIDGET_ID
```

Logged-in buyers can open support tickets from `/support`, attach a recent order when relevant, and reply from the ticket detail page. Admins manage tickets in Filament under Customer Support, including status, priority, filters, and staff replies.

## Order Tracking

Task 21 adds order tracking history for storefront, account, vendor, and Filament admin workflows. Guests track with order number plus email at `/track-order`; logged-in buyers see the timeline on their order detail page; vendors can add shipment updates for their own dropship orders; admins can manage tracking events from the Order and Dropship Order Filament resources.

The local app uses safe near-real-time polling because broadcasting is currently configured with the `log` driver. The `OrderTrackingUpdated` event and `OrderTrackingService` are ready for future carrier webhook or broadcast integration without adding production credentials.

## Oracle Cloud Deployment Preparation

Task 15 prepared deployment documentation and safe sample configuration for a future Oracle Cloud production setup. The actual deployment has not been performed because the Oracle Cloud account/server is not available yet.

- Deployment guide: [docs/deployment/oracle-cloud.md](docs/deployment/oracle-cloud.md)
- Manual checklist: [docs/deployment/deploy-checklist.md](docs/deployment/deploy-checklist.md)
- Example production environment: [.env.production.example](.env.production.example)
- Sample Nginx and Supervisor configs are in [docs/deployment](docs/deployment).

Do not commit real credentials, API keys, tokens, SSH keys, passwords, or a real Laravel `APP_KEY`. Replace placeholders only on the actual server when the owner is ready to deploy.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
