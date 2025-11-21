# Car Inspection Expert System

A comprehensive car inspection form with progressive image upload, draft saving, and PDF generation.

## Features

- ✅ 23-step inspection form
- ✅ Progressive image upload (uploads immediately when selected)
- ✅ Draft system with auto-save
- ✅ PDF generation with all inspection data
- ✅ Email delivery
- ✅ WordPress hosting compatible

## Quick Start

### Local Development

```bash
php -S localhost:8000
```

Then visit: http://localhost:8000

### WordPress Deployment

1. Upload all files to your WordPress hosting
2. Set folder permissions to 755:
   - uploads/
   - uploads/drafts/
   - pdfs/
   - logs/
3. Install Composer dependencies: `composer install`
4. Done!

## How It Works

### Progressive Upload
- Images upload immediately when selected (1-3 seconds each)
- No 20-file limit issue (each upload is separate)
- All images stored in draft automatically
- Final submission only sends paths (fast!)

### Draft System
- Auto-saves form data
- Stores uploaded images
- Resume from any point
- Refresh page → everything restored

## Requirements

- PHP 7.4+
- Composer (for mPDF)
- Write permissions on uploads/, pdfs/, logs/, tmp/
- PHP memory_limit: 2048M (auto-configured)

## File Structure

```
project/
├── index.php              # Main form
├── submit.php             # Form submission handler
├── generate-pdf.php       # PDF generation
├── upload-image.php       # Single image upload (AJAX)
├── save-draft.php         # Draft saving
├── load-draft.php         # Draft loading
├── delete-draft.php       # Draft deletion
├── send-email.php         # Email delivery
├── script.js              # Frontend logic
├── style.css              # Styling
├── config.php             # Configuration
├── auto-config.php        # Auto PHP configuration
├── image-optimizer.php    # Image optimization
├── uploads/               # Uploaded files
├── pdfs/                  # Generated PDFs
└── logs/                  # Error logs
```

## Configuration

Edit `config.php` to configure:
- Email settings (SMTP)
- File upload limits
- PDF settings
- Application settings

## Support

For issues or questions, check the error logs:
- logs/error.log
- logs/php_errors.log

## License

Proprietary - For client use only
