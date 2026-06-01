<?php

namespace App\Command;

use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:populate-tags',
    description: 'Seed the tag table with common Apple product tags.',
)]
class PopulateTagCommand extends Command
{
    private const TAGS = [
        // Era
        'vintage', 'retro', 'modern', 'classic',
        // Form factor
        'portable', 'desktop', 'wearable', 'handheld',
        // Tier
        'pro', 'consumer', 'enterprise',
        // Platform
        'iOS', 'macOS', 'watchOS', 'tvOS', 'iPadOS',
        // Hardware traits
        'touchscreen', 'wireless', 'bluetooth', 'cellular',
        'usb-c', 'lightning', '30-pin',
        // Chip
        'intel', 'powerpc', 'apple silicon', 'm1', 'm2',
        // Category
        'audio', 'camera', 'display', 'storage', 'networking',
    ];

    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $existing = $this->em->getRepository(Tag::class)->findAll();
        if (!empty($existing)) {
            $io->warning('Tag table already populated. Skipping.');

            return Command::SUCCESS;
        }

        foreach (self::TAGS as $name) {
            $tag = new Tag();
            $tag->setName($name);
            $this->em->persist($tag);
        }

        $this->em->flush();
        $io->success(count(self::TAGS) . ' tags inserted.');

        return Command::SUCCESS;
    }
}