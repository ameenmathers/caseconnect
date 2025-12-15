<?php

use App\Jobs\ProcessCallRecording;
use App\Models\Call;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
    Queue::fake();
});

describe('index', function () {
    it('displays the calls list page', function () {
        $response = $this->get(route('calls.index'));

        $response->assertStatus(200);
        $response->assertSee('Call Recordings');
    });

    it('lists all calls', function () {
        Call::factory()->create(['original_filename' => 'call-one.mp3']);
        Call::factory()->create(['original_filename' => 'call-two.mp3']);

        $response = $this->get(route('calls.index'));

        $response->assertSee('call-one.mp3');
        $response->assertSee('call-two.mp3');
    });

    it('shows empty state when no calls exist', function () {
        $response = $this->get(route('calls.index'));

        $response->assertSee('No calls uploaded');
    });
});

describe('create', function () {
    it('displays the upload form', function () {
        $response = $this->get(route('calls.create'));

        $response->assertStatus(200);
        $response->assertSee('Upload Call Recording');
    });
});

describe('store', function () {
    it('creates a new call record', function () {
        $file = UploadedFile::fake()->create('recording.mp3', 1024, 'audio/mpeg');

        $response = $this->post(route('calls.store'), [
            'audio' => $file,
        ]);

        expect(Call::count())->toBe(1);
        $response->assertRedirect();
    });

    it('stores the uploaded file', function () {
        $file = UploadedFile::fake()->create('recording.mp3', 1024, 'audio/mpeg');

        $this->post(route('calls.store'), [
            'audio' => $file,
        ]);

        $call = Call::first();
        Storage::assertExists($call->file_path);
    });

    it('dispatches the processing job', function () {
        $file = UploadedFile::fake()->create('recording.mp3', 1024, 'audio/mpeg');

        $this->post(route('calls.store'), [
            'audio' => $file,
        ]);

        Queue::assertPushed(ProcessCallRecording::class);
    });

    it('validates file is required', function () {
        $response = $this->post(route('calls.store'), []);

        $response->assertSessionHasErrors('audio');
    });

    it('validates file type', function () {
        $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');

        $response = $this->post(route('calls.store'), [
            'audio' => $file,
        ]);

        $response->assertSessionHasErrors('audio');
    });

    it('validates file size', function () {
        $file = UploadedFile::fake()->create('large-file.mp3', 60000, 'audio/mpeg');

        $response = $this->post(route('calls.store'), [
            'audio' => $file,
        ]);

        $response->assertSessionHasErrors('audio');
    });

    it('redirects to call show page after upload', function () {
        $file = UploadedFile::fake()->create('recording.mp3', 1024, 'audio/mpeg');

        $response = $this->post(route('calls.store'), [
            'audio' => $file,
        ]);

        $call = Call::first();
        $response->assertRedirect(route('calls.show', $call));
    });
});

describe('show', function () {
    it('displays call details', function () {
        $call = Call::factory()->completed()->create([
            'original_filename' => 'important-call.mp3',
            'summary' => 'This is a summary of the call.',
        ]);

        $response = $this->get(route('calls.show', $call));

        $response->assertStatus(200);
        $response->assertSee('important-call.mp3');
        $response->assertSee('This is a summary of the call.');
    });

    it('displays lead score', function () {
        $call = Call::factory()->completed()->create([
            'lead_score' => 85,
        ]);

        $response = $this->get(route('calls.show', $call));

        $response->assertStatus(200);
        $response->assertSee('85');
    });

    it('displays keywords detected', function () {
        $call = Call::factory()->completed()->create([
            'keywords_detected' => ['car accident', 'injury'],
        ]);

        $response = $this->get(route('calls.show', $call));

        $response->assertStatus(200);
        $response->assertSee('car accident');
        $response->assertSee('injury');
    });

    it('displays next actions', function () {
        $call = Call::factory()->completed()->create([
            'next_actions' => ['Schedule follow-up call', 'Send intake questionnaire'],
        ]);

        $response = $this->get(route('calls.show', $call));

        $response->assertStatus(200);
        $response->assertSee('Schedule follow-up call');
    });

    it('shows processing message for pending calls', function () {
        $call = Call::factory()->create(['status' => 'processing']);

        $response = $this->get(route('calls.show', $call));

        $response->assertSee('Processing in progress');
    });

    it('shows error message for failed calls', function () {
        $call = Call::factory()->failed()->create([
            'error_message' => 'Transcription service unavailable',
        ]);

        $response = $this->get(route('calls.show', $call));

        $response->assertSee('Transcription service unavailable');
    });
});

describe('destroy', function () {
    it('deletes the call record', function () {
        $call = Call::factory()->create();

        $response = $this->delete(route('calls.destroy', $call));

        expect(Call::count())->toBe(0);
        $response->assertRedirect(route('calls.index'));
    });

    it('shows success message after deletion', function () {
        $call = Call::factory()->create();

        $response = $this->delete(route('calls.destroy', $call));

        $response->assertSessionHas('success');
    });
});

describe('reanalyze', function () {
    it('reanalyzes a call with existing transcript', function () {
        $call = Call::factory()->completed()->create([
            'transcript' => 'This is a test transcript about a car accident.',
        ]);

        $response = $this->post(route('calls.reanalyze', $call));

        $response->assertRedirect();
        $response->assertSessionHas('success');
    });

    it('fails to reanalyze a call without transcript', function () {
        $call = Call::factory()->create([
            'transcript' => null,
        ]);

        $response = $this->post(route('calls.reanalyze', $call));

        $response->assertSessionHas('error');
    });
});

