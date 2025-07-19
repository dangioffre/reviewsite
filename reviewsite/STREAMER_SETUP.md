# Streamer Profile Setup Guide

This guide will help you configure the streaming platform integrations for the streamer profile system.

## Required API Configurations

### 1. Twitch Integration

1. **Create a Twitch Application:**
   - Go to [Twitch Developer Console](https://dev.twitch.tv/console)
   - Click "Register Your Application"
   - Fill in the details:
     - Name: Your app name
     - OAuth Redirect URLs: `http://your-domain.com/auth/twitch/callback`
     - Category: Website Integration

2. **Get Your Credentials:**
   - Copy the Client ID
   - Generate and copy the Client Secret

3. **Add to .env:**
   ```env
   TWITCH_CLIENT_ID=your_client_id_here
   TWITCH_CLIENT_SECRET=your_client_secret_here
   TWITCH_REDIRECT_URI=http://your-domain.com/auth/twitch/callback
   ```

### 2. YouTube Integration

1. **Create a Google Cloud Project:**
   - Go to [Google Cloud Console](https://console.cloud.google.com/)
   - Create a new project or select existing one

2. **Enable YouTube Data API v3:**
   - Go to APIs & Services > Library
   - Search for "YouTube Data API v3"
   - Click Enable

3. **Create OAuth 2.0 Credentials:**
   - Go to APIs & Services > Credentials
   - Click "Create Credentials" > "OAuth 2.0 Client IDs"
   - Application type: Web application
   - Authorized redirect URIs: `http://your-domain.com/auth/youtube/callback`

4. **Add to .env:**
   ```env
   YOUTUBE_CLIENT_ID=your_client_id_here
   YOUTUBE_CLIENT_SECRET=your_client_secret_here
   YOUTUBE_REDIRECT_URI=http://your-domain.com/auth/youtube/callback
   ```

### 3. Kick Integration

1. **Create a Kick Application:**
   - Go to [Kick Developer Portal](https://kick.com/developer) (if available)
   - Register your application
   - Set redirect URI: `http://your-domain.com/auth/kick/callback`

2. **Add to .env:**
   ```env
   KICK_CLIENT_ID=your_client_id_here
   KICK_CLIENT_SECRET=your_client_secret_here
   KICK_REDIRECT_URI=http://your-domain.com/auth/kick/callback
   ```

## Required Laravel Packages

Make sure you have Laravel Socialite installed:

```bash
composer require laravel/socialite
```

For additional platform support, you may need:

```bash
composer require socialiteproviders/twitch
composer require socialiteproviders/youtube
composer require socialiteproviders/kick
```

## Configuration Files

### config/services.php
The streaming platform configurations are already set up in `config/services.php`. They will automatically read from your environment variables.

### Socialite Configuration
Add to your `config/app.php` providers array if using SocialiteProviders:

```php
'providers' => [
    // Other providers...
    \SocialiteProviders\Manager\ServiceProvider::class,
],
```

And in your `app/Providers/EventServiceProvider.php`:

```php
protected $listen = [
    \SocialiteProviders\Manager\SocialiteWasCalled::class => [
        'SocialiteProviders\\Twitch\\TwitchExtendSocialite@handle',
        'SocialiteProviders\\YouTube\\YouTubeExtendSocialite@handle',
        'SocialiteProviders\\Kick\\KickExtendSocialite@handle',
    ],
];
```

## Testing the Integration

1. **Start your development server:**
   ```bash
   php artisan serve
   ```

2. **Visit the streamer creation page:**
   - Go to `/streamers/create`
   - Click on a platform button
   - You should be redirected to the platform's OAuth page

3. **Check for errors:**
   - Monitor `storage/logs/laravel.log` for any OAuth errors
   - Ensure your redirect URIs match exactly

## Troubleshooting

### Common Issues:

1. **"Client ID not found" error:**
   - Double-check your environment variables
   - Restart your development server after adding .env variables

2. **"Redirect URI mismatch" error:**
   - Ensure the redirect URI in your platform app matches exactly
   - Check for http vs https differences

3. **"Invalid state" error:**
   - This usually indicates session issues
   - Clear your browser cache and try again

4. **Platform API rate limits:**
   - Each platform has different rate limits
   - Implement caching and retry logic as needed

## Security Considerations

1. **Keep secrets secure:**
   - Never commit .env files to version control
   - Use different credentials for development and production

2. **Use HTTPS in production:**
   - All OAuth redirect URIs should use HTTPS in production
   - Update your platform app configurations accordingly

3. **Validate tokens:**
   - The system automatically handles token validation and refresh
   - Monitor for expired or invalid tokens

## Next Steps

After setting up the API configurations:

1. Users can visit `/streamers/create` to connect their accounts
2. The OAuth flow will create their streamer profiles automatically
3. Profiles will be pending approval by default
4. Admins can approve profiles through the admin panel

For more detailed information about the streamer profile system, see the main documentation.