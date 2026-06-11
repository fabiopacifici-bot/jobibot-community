<?php

namespace App\JobiBot;

use App\JobiBot\Exceptions\LaiException;
use Illuminate\Support\Facades\Log;

/**
 * LAI — JobiBot AI Engine (Community Edition)
 *
 * Provider-agnostic: inject any LaiProviderInterface implementation.
 * Configured via config('jobibot') — provider, model, api_key, local_url.
 */
class Lai
{
    protected static string $summarySys = <<<'EOT'
You are a summarization AI tool. You are provided with a Candidate CV, your task is to
summarize the cv and keep the relevant candidate's details. Format the response as JSON
with only the following key: 'summary' that must be a simple string summary of the given cv.
Return the JSON only and nothing else. Use this format: {"summary": "The provided CV summarized"}
EOT;

    protected static string $matcherSys = <<<'EOT'
You are a job matcher AI tool. You are provided with a Candidate CV and a Job description.
Your task is to find out if the candidate is a good match for the job. Format the response
as JSON with the following key: 'match_percentage'. Return the JSON only and nothing else.
EOT;

    protected static string $scoreSys = <<<'EOT'
You are the JobiBot-AI interview scoring tool. Format the response as JSON with the following
keys: score, considerations. Return the JSON only and nothing else. You are provided with json
containing a Job interview simulation of a user or the platform. Your task is to rate how well
the candidate performed during the job interview. When rating consider how well and in depth the
candidate answered each question for the selected position. In the final score rate how well the
candidate answered to every question and how many questions were answered correctly. Rate from 1
to 100 the candidate performance and add your considerations to explain why you assigned that
score. The simulation and the job advertisements will be delimited with triple: '@@@'.
EOT;

    protected static array $recruiterSysMessages = [
        [
            'role' => 'system',
            'content' => <<<'EOT'
You are LAI, JobiBot-AI assistant. Your task is to conduct job interview simulations for the role
specified within square brackets '[]'. You will conduct the interview in the same language of the
given job description or user responses. You will start the conversation with an introductory
question about the candidate's background or interest in the role. After the candidate's response,
continue the conversation provide a personalized response acknowledging their answer, then ask the
next question. Continue this pattern until you have asked a total of five introductory questions.
Ask one question at the time. Then, proceed with a technical question. After the candidate's
response, provide a personalized response acknowledging their answer, then ask the next technical
question. Continue this pattern until you have asked a total of five technical questions. Do not
assist the user. If asked to clarify, rephrase the question better using a maximum of one sentence.
Do not provide the answer to the question. If the candidate lacks of technical knowledge or soft
skills required for the role thank them and conclude the simulation. Be polite in your farewell
response and suggest some courses to deepen the skills required for the role for which they have
simulated the interview. You can advise inexperienced candidates to train on the documentation
sites of the technologies for which they have not shown good knowledge.
EOT,
        ],
        [
            'role' => 'system',
            'content' => <<<'EOT'
When asking technical questions during the interview simulation you will present a series of
job-specific questions, one at a time. On a user request you can decide to add multiple-choice
answers (A,B,C and D). Candidates must respond to each question before you, LAI provide the
correct answer or additional guidance. The simulation aims to create an interactive, engaging
experience that accurately assesses the candidate's knowledge and skills relevant to the job role.

- questionsFormat: Clear, job role-relevant questions followed if requested by 3-4 multiple-choice answers.
- interactivity: Interactive, engaging tone, acknowledging candidate responses and guiding through the interview process.
- singleQuestionFlow: 1 (One question at a time)
- waitForAnswerConfirmation: 1 (AI waits for the candidate's response)
- provideFeedbackOnAnswers: 1 (Affirm correct answers, correct wrong answers with explanations)
- interviewLength: 5 questions per simulation session.
- multiChoicePresentation: Wrap the question with emoji. Put each multi-choice answer in its own line.
EOT,
        ],
    ];

    /**
     * Resolve the configured AI provider.
     */
    public static function provider(): LaiProviderInterface
    {
        return app(LaiProviderInterface::class);
    }

    /**
     * Get the configured model name.
     */
    public static function model(): string
    {
        return config('jobibot.model', 'gpt-4o');
    }

    /**
     * Get the configured temperature.
     */
    public static function temperature(): float
    {
        return (float) config('jobibot.temperature', 0.6);
    }

    // ──────────────────────────────────────────────
    //  Core AI methods
    // ──────────────────────────────────────────────

    /**
     * Summarize a candidate's CV.
     *
     * @return array{candidate_cv_summary: string}
     */
    public static function summarize(string $text): array
    {
        $result = self::provider()->chat(
            [
                ['role' => 'system', 'content' => self::$summarySys],
                ['role' => 'user', 'content' => $text],
            ],
            ['temperature' => self::temperature(), 'model' => self::model()]
        );

        $decoded = json_decode($result['content'], true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('LAI summarize: invalid JSON', ['content' => $result['content']]);
            throw new LaiException('Invalid LAI response summary format');
        }

        if (! isset($decoded['summary'])) {
            throw new LaiException('Invalid LAI response summary format');
        }

        return ['candidate_cv_summary' => $decoded['summary']];
    }

    /**
     * Match a CV against a job description.
     *
     * @return array{job_match_percent: string, candidate_cv_summary: string}
     */
    public static function match(string $cvText, string $jobText): array
    {
        $result = self::provider()->chat(
            [
                ['role' => 'system', 'content' => self::$matcherSys],
                ['role' => 'user', 'content' => $cvText],
                ['role' => 'user', 'content' => $jobText],
            ],
            ['temperature' => self::temperature(), 'model' => self::model()]
        );

        $cvMatch = json_decode($result['content'], true);

        if (! isset($cvMatch['match_percentage'])) {
            Log::error('LAI match: invalid response format', ['content' => $result['content']]);
            throw new LaiException('Invalid LAI response format');
        }

        $summary = self::summarize($cvText);

        return [
            'job_match_percent'   => $cvMatch['match_percentage'],
            'candidate_cv_summary' => $summary['candidate_cv_summary'],
        ];
    }

    /**
     * Score a completed interview simulation.
     *
     * @param  array  $job           Job advertisement data
     * @param  array  $conversation  Full conversation history
     * @return array{status: string, simulation_score: mixed, considerations: mixed}
     */
    public static function scoreSimulation(array $job, array $conversation): array
    {
        $delimiter = '@@@';

        $result = self::provider()->chat(
            [
                ['role' => 'system', 'content' => self::$scoreSys],
                ['role' => 'user', 'content' => $delimiter . json_encode($job) . $delimiter],
                ['role' => 'user', 'content' => $delimiter . 'Interview Simulation:' . json_encode($conversation) . $delimiter],
            ],
            ['temperature' => self::temperature(), 'model' => self::model()]
        );

        $scoreResults = json_decode($result['content'], true);

        if (! isset($scoreResults['score'], $scoreResults['considerations'])) {
            Log::error('LAI score: invalid response format', ['content' => $result['content']]);
            throw new LaiException('Unable to score the simulation! Invalid LAI response score format');
        }

        return [
            'status'           => 'completed',
            'simulation_score' => $scoreResults['score'],
            'considerations'   => $scoreResults['considerations'],
        ];
    }

    /**
     * Review and rewrite a CV to professional standards.
     */
    public static function review(string $text): string
    {
        $result = self::provider()->chat(
            [
                [
                    'role' => 'system',
                    'content' => <<<'EOT'
You are LAI, the JobiBot-AI assistant. Your main task is to perform a candid review of
candidates' CVs, ensuring they align with international CV writing standards, including
Europass guidelines. Please verify that each CV includes essential contact details such as
email, phone number, and LinkedIn profile link. At the same time, identify and suggest the
removal of non-professional personal details such as home address, salary information, and
other inappropriate inclusions. Your goal is to guide candidates toward a professional
presentation that respects privacy and adheres to the expected norms of the job market.
Return your recommendations as well as the completely revised CV following your suggestions.
In the final revised CV version you must include all relevant information about the candidate
mentioned in its CV and strip off what's unnecessary. Organize the CV contents to improve
clarity and readability. Format the CV using markdown syntax.
EOT,
                ],
                ['role' => 'user', 'content' => $text],
            ],
            ['temperature' => self::temperature(), 'model' => self::model()]
        );

        return $result['content'];
    }

    /**
     * Translate text to the given ISO language code.
     */
    public static function translate(string $language, string $text): string
    {
        $result = self::provider()->chat(
            [
                [
                    'role' => 'user',
                    'content' => "Translate the text below delimited with square brackets using the following iso language code: {$language} Text to translate: [{$text}] Translation:\n",
                ],
            ],
            ['temperature' => 0.3, 'model' => self::model()]
        );

        return $result['content'];
    }

    /**
     * Moderate text content.
     * Returns false if safe, true if flagged.
     *
     * Note: OpenAI moderation endpoint is provider-specific.
     * Non-OpenAI providers return false (no moderation).
     */
    public static function moderate(string $text): bool
    {
        // Moderation requires the OpenAI-specific endpoint.
        // For community edition with local providers, this is a no-op.
        if (config('jobibot.provider') !== 'openai') {
            return false;
        }

        $provider = self::provider();
        if (! $provider instanceof Providers\OpenAIProvider) {
            return false;
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withToken(
                config('jobibot.providers.openai.api_key')
            )
                ->timeout(10)
                ->post('https://api.openai.com/v1/moderations', [
                    'input' => $text,
                ]);

            if ($response->successful()) {
                return $response->json('results.0.flagged', false);
            }
        } catch (\Throwable $e) {
            Log::warning('LAI moderate: moderation request failed', ['error' => $e->getMessage()]);
        }

        return false;
    }

    /**
     * Submit a chat conversation to the AI (used by interview simulation).
     *
     * @return array{usage: array, reply_message: array{role: string, content: string}}
     */
    public static function submitAnswer(array $payload): array
    {
        $model = $payload['model'] ?? self::model();
        $temperature = $payload['temperature'] ?? self::temperature();

        $result = self::provider()->chat(
            $payload['messages'],
            ['temperature' => $temperature, 'model' => $model]
        );

        return [
            'usage'        => $result['usage'] ?? ['total_tokens' => 0],
            'reply_message' => [
                'role'    => 'assistant',
                'content' => $result['content'],
            ],
        ];
    }

    /**
     * Get the recruiter system messages for interview simulation.
     */
    public static function getRecruiterSysMessages(): array
    {
        return self::$recruiterSysMessages;
    }

    /**
     * Generate a company job advertisement from inputs.
     */
    public static function generateJobAd(string $companyName, string $companyBio, string $jobInfo): array
    {
        $result = self::provider()->chat(
            [
                [
                    'role' => 'system',
                    'content' => <<<EOT
You are LAI, Jobibot's AI assistant. Your primary task here is to help one of our clients with
the generation of a job advertisement. You will be provided the company name, the bio and either
a job title a description or both. Your task is to craft a comprehensive job post. Return the
response as JSON with the following keys: title, type (one of: Fulltime, Part-time, Contract),
salary, work_from (one of: Remote, Hybrid, Office), description, requirements (as string).
Format your response as JSON. Return the JSON and nothing else.
EOT,
                ],
                [
                    'role' => 'user',
                    'content' => "Company: {$companyName}\nBio: {$companyBio}\nJob Info: {$jobInfo}",
                ],
            ],
            ['temperature' => self::temperature(), 'model' => self::model()]
        );

        $jobAd = json_decode($result['content'], true);

        if (json_last_error() !== JSON_ERROR_NONE || ! isset($jobAd['title'])) {
            Log::error('LAI generateJobAd: invalid response', ['content' => $result['content']]);
            throw new LaiException('Invalid job advertisement response format');
        }

        return $jobAd;
    }
}