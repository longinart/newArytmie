<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $this->verifyTurnstileIfEnabled($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:10000'],
            'consented_to_processing' => ['accepted'],
        ]);

        ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'] ?? null,
            'message' => $validated['message'],
            'consented_to_processing' => true,
        ]);

        return redirect()
            ->route('home')
            ->withFragment('kontakt')
            ->with('contact_status', 'Děkujeme, zpráva byla odeslána. Ozveme se, jakmile to bude možné.');
    }

    private function verifyTurnstileIfEnabled(Request $request): void
    {
        if (! config('services.turnstile.enabled')) {
            return;
        }

        $token = $request->string('cf-turnstile-response')->toString();
        if ($token === '') {
            throw ValidationException::withMessages([
                'cf-turnstile-response' => 'Potvrďte prosím, že nejste robot.',
            ]);
        }

        $secret = (string) config('services.turnstile.secret_key');
        if ($secret === '') {
            throw ValidationException::withMessages([
                'cf-turnstile-response' => 'Kontaktní formulář není správně nakonfigurován. Zkuste to prosím později.',
            ]);
        }

        $response = Http::asForm()
            ->timeout(10)
            ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $request->ip(),
            ])
            ->json();

        if (! is_array($response) || empty($response['success'])) {
            throw ValidationException::withMessages([
                'cf-turnstile-response' => 'Ověření se nezdařilo. Zkuste to prosím znovu.',
            ]);
        }
    }
}
