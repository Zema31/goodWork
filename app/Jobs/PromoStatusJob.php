<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PromoStatusJob implements ShouldQueue
{
    use Queueable;

    private $promo;
    private $status;

    /**
     * Create a new job instance.
     */
    public function __construct(object $promo, string $status)
    {
        $this->promo = $promo;
        $this->status = $status;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->promo->status = $this->status;
        $this->promo->save();
    }
}
