<?php

namespace App\Command;

use App\Entity\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:populate-product-type',
    description: 'Seed the product_type table with all known Apple product categories.',
)]
class PopulateProductTypeCommand extends Command
{
    private const TYPES = [
        'iPhone',
        'iPad',
        'iPod',
        'Mac',
        'iMac',
        'Mac mini',
        'Mac Pro',
        'Mac Studio',
        'MacBook',
        'MacBook Air',
        'MacBook Pro',
        'Apple Watch',
        'AirPods',
        'HomePod',
        'Apple TV',
        'Accessory',
        'Display',
        'Software',
    ];

    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $existing = $this->em->getRepository(ProductType::class)->findAll();
        if (!empty($existing)) {
            $io->warning('Product type table already populated. Skipping.');

            return Command::SUCCESS;
        }

        foreach (self::TYPES as $type) {
            $productType = new ProductType();
            $productType->setType($type);
            $this->em->persist($productType);
        }

        $this->em->flush();
        $io->success(count(self::TYPES) . ' product types inserted.');

        return Command::SUCCESS;
    }
}
