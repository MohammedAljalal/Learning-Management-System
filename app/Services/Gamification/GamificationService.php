<?php

declare(strict_types=1);

namespace App\Services\Gamification;

use App\Models\User;
use App\Models\XpTransaction;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;

class GamificationService extends Service
{
    public function __construct(LoggerInterface $logger)
    {
        parent::__construct($logger);
    }

    /**
     * Award XP to a user and return the transaction.
     */
    public function awardXp(User $user, int $amount, string $description): XpTransaction
    {
        return DB::transaction(function () use ($user, $amount, $description) {
            $transaction = $user->xpTransactions()->create([
                'amount' => $amount,
                'description' => $description,
            ]);

            $this->logger->info("Awarded XP", [
                'user_id' => $user->id,
                'amount' => $amount,
                'description' => $description
            ]);

            return $transaction;
        });
    }

    /**
     * Get the XP needed for the next level.
     */
    public function getXpForNextLevel(User $user): int
    {
        $currentLevel = $user->level;
        $xpForNextLevel = $currentLevel * 500;
        return max(0, $xpForNextLevel - $user->total_xp);
    }
}
