<?php

use App\Actions\Order\MatchOrderAction;
use App\Jobs\MatchOrderJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('test-order-book', function () {
    MatchOrderJob::dispatchSync();
});

Artisan::command('match-orders', function () {
    app(MatchOrderAction::class)->execute();
});
