# CaseConnect - AI Call Summarizer & Lead Scoring Engine

A Laravel application that transcribes call recordings, analyzes sentiment, and automatically scores leads for personal injury law firms.

## Features

- **Audio Transcription** - Upload MP3/WAV files and get AI-powered transcriptions via AssemblyAI
- **Lead Scoring (0-100)** - Automatic scoring based on keywords, sentiment, urgency, and engagement
- **Eligibility Detection** - Determines if a caller qualifies as a potential client
- **Sentiment Analysis** - Identifies positive, negative, or neutral caller sentiment
- **Keyword Detection** - Flags important terms like "car accident", "injury", "insurance denied"
- **Next Actions** - AI-generated recommendations for follow-up
- **Dashboard Analytics** - Real-time metrics on call volume, conversion rates, and lead quality

## Tech Stack

- **Backend:** Laravel 11, PHP 8.2+
- **Frontend:** Blade Components, Tailwind CSS 4
- **Database:** SQLite (dev) / MySQL/PostgreSQL (prod)
- **Testing:** Pest PHP (107 tests)
- **API:** AssemblyAI for transcription

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js & npm
- AssemblyAI API key

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/yourusername/caseconnect.git
cd caseconnect
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure your `.env` file

```env
# Database (SQLite for local dev)
DB_CONNECTION=sqlite

# AssemblyAI API Key (required for transcription)
ASSEMBLYAI_API_KEY=your_api_key_here

# Queue (use 'sync' for local, 'database' or 'redis' for production)
QUEUE_CONNECTION=sync
```

### 5. Run migrations and seed

```bash
php artisan migrate --seed
```

### 6. Build assets

```bash
npm run build
```

### 7. Start the development server

```bash
# Option 1: Run everything together
composer dev

# Option 2: Run separately
php artisan serve
npm run dev
php artisan queue:work  # If using database queue
```

Visit **http://localhost:8000**

## Testing

```bash
# Run all 107 tests
php artisan test

# Run specific test suites
php artisan test --filter=LeadScoring
php artisan test --filter=TranscriptionService
php artisan test --filter=CallController

# Test AssemblyAI integration (requires API key)
php artisan test:transcription
```

## Project Structure

```
app/
├── Http/Controllers/
│   ├── CallController.php       # Call CRUD operations
│   └── DashboardController.php  # Dashboard statistics
├── Jobs/
│   └── ProcessCallRecording.php # Background transcription job
├── Models/
│   └── Call.php                 # Call model with scopes
├── Services/
│   ├── CallAnalysisService.php      # Orchestrates analysis pipeline
│   ├── LeadScoringService.php       # Calculates lead scores
│   ├── SentimentAnalysisService.php # Analyzes sentiment
│   └── TranscriptionService.php     # AssemblyAI integration
└── Traits/
    ├── Analyzable.php           # Keyword detection utilities
    └── HasLeadScore.php         # Score labels and colors

resources/views/
├── components/                  # Reusable Blade components
│   ├── button.blade.php
│   ├── card.blade.php
│   ├── badge.blade.php
│   └── score-indicator.blade.php
├── calls/                       # Call views
│   ├── index.blade.php
│   ├── show.blade.php
│   └── upload.blade.php
└── dashboard.blade.php

tests/
├── Feature/                     # HTTP/Integration tests
│   ├── CallControllerTest.php
│   └── DashboardTest.php
└── Unit/                        # Unit tests
    ├── Models/CallTest.php
    ├── Services/
    │   ├── LeadScoringServiceTest.php
    │   ├── SentimentAnalysisServiceTest.php
    │   └── TranscriptionServiceTest.php
    └── Traits/
        ├── AnalyzableTest.php
        └── HasLeadScoreTest.php
```

## How Lead Scoring Works

The scoring algorithm evaluates calls across multiple dimensions:

| Factor | Weight | Description |
|--------|--------|-------------|
| Keywords | 30% | Presence of terms like "car accident", "injury", "settlement" |
| Sentiment | 20% | Positive sentiment indicates engaged caller |
| Duration | 15% | Longer calls suggest genuine interest |
| Urgency | 15% | Words like "urgent", "immediately", "emergency" |
| Contact Info | 10% | Caller provides phone/email |
| Engagement | 10% | Word count and conversation depth |

### Score Labels

| Score | Label | Color |
|-------|-------|-------|
| 80-100 | Hot Lead | Green |
| 60-79 | Warm Lead | Amber |
| 40-59 | Lukewarm | Orange |
| 20-39 | Cold Lead | Rose |
| 0-19 | Very Cold | Gray |

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | Dashboard with statistics |
| GET | `/calls` | List all calls |
| GET | `/calls/create` | Upload form |
| POST | `/calls` | Upload new call |
| GET | `/calls/{id}` | View call details |
| POST | `/calls/{id}/reanalyze` | Re-run analysis |
| DELETE | `/calls/{id}` | Delete call |

## Deployment

### Railway

1. Connect your GitHub repository
2. Add environment variables:
   - `APP_KEY` (generate with `php artisan key:generate --show`)
   - `ASSEMBLYAI_API_KEY`
   - `DB_CONNECTION=pgsql` (or mysql)
   - Database credentials from Railway
3. Deploy

### Environment Variables for Production

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=pgsql
DB_HOST=your-host
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password

QUEUE_CONNECTION=database
ASSEMBLYAI_API_KEY=your-api-key
```

## License

MIT License - feel free to use this project for your portfolio or production applications.
