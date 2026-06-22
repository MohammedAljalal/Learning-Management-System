<?php

declare(strict_types=1);

namespace App\Services\Courses;

use App\Models\Section;
use App\Repositories\Contracts\SectionRepositoryContract;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;
use Throwable;

class SectionService extends Service
{
    public function __construct(
        LoggerInterface $logger,
        private readonly SectionRepositoryContract $sectionRepository
    ) {
        parent::__construct($logger);
    }

    /**
     * Create a new section.
     *
     * @throws Throwable
     */
    public function createSection(array $data): Section
    {
        return DB::transaction(function () use ($data) {
            $section = $this->sectionRepository->create($data);
            $this->logger->info("Section created.", ['section_id' => $section->id]);
            return $section;
        });
    }

    /**
     * Update an existing section.
     *
     * @throws Throwable
     */
    public function updateSection(Section $section, array $data): Section
    {
        return DB::transaction(function () use ($section, $data) {
            $updatedSection = $this->sectionRepository->update($section->id, $data);
            $this->logger->info("Section updated.", ['section_id' => $updatedSection->id]);
            return $updatedSection;
        });
    }

    /**
     * Delete a section.
     *
     * @throws Throwable
     */
    public function deleteSection(Section $section): void
    {
        DB::transaction(function () use ($section) {
            $this->sectionRepository->delete($section->id);
            $this->logger->info("Section deleted.", ['section_id' => $section->id]);
        });
    }
}
