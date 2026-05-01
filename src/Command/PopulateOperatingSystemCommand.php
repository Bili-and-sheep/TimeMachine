<?php

namespace App\Command;

use App\Entity\OperatingSystem;
use App\Enum\OsFamily;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:populate-os',
    description: 'Seed the operating_system table with all known Apple OS versions.',
)]
class PopulateOperatingSystemCommand extends Command
{
    private const VERSIONS = [
        'iOS' => [
            '1.0', '2.0', '3.0', '3.1', '3.2',
            '4.0', '4.1', '4.2', '4.3',
            '5.0', '5.1',
            '6.0', '6.1',
            '7.0', '7.1',
            '8.0', '8.1', '8.2', '8.3', '8.4',
            '9.0', '9.1', '9.2', '9.3',
            '10.0', '10.1', '10.2', '10.3',
            '11.0', '11.1', '11.2', '11.3', '11.4',
            '12.0', '12.1', '12.2', '12.3', '12.4',
            '13.0', '13.1', '13.2', '13.3', '13.4', '13.5', '13.6', '13.7',
            '14.0', '14.1', '14.2', '14.3', '14.4', '14.5', '14.6', '14.7', '14.8',
            '15.0', '15.1', '15.2', '15.3', '15.4', '15.5', '15.6', '15.7', '15.8',
            '16.0', '16.1', '16.2', '16.3', '16.4', '16.5', '16.6', '16.7',
            '17.0', '17.1', '17.2', '17.3', '17.4', '17.5', '17.6', '17.7',
            '18.0', '18.1', '18.2', '18.3', '18.4',
        ],
        'macOS' => [
            '10.0 Cheetah', '10.1 Puma', '10.2 Jaguar', '10.3 Panther', '10.4 Tiger',
            '10.5 Leopard', '10.6 Snow Leopard', '10.7 Lion', '10.8 Mountain Lion',
            '10.9 Mavericks', '10.10 Yosemite', '10.11 El Capitan',
            '10.12 Sierra', '10.13 High Sierra', '10.14 Mojave', '10.15 Catalina',
            '11 Big Sur', '12 Monterey', '13 Ventura', '14 Sonoma', '15 Sequoia',
        ],
        'watchOS' => [
            '1.0', '2.0', '3.0', '3.1', '3.2',
            '4.0', '4.1', '4.2', '4.3',
            '5.0', '5.1', '5.2', '5.3',
            '6.0', '6.1', '6.2',
            '7.0', '7.1', '7.2', '7.3', '7.4', '7.5', '7.6',
            '8.0', '8.1', '8.2', '8.3', '8.4', '8.5', '8.6', '8.7',
            '9.0', '9.1', '9.2', '9.3', '9.4', '9.5', '9.6',
            '10.0', '10.1', '10.2', '10.3', '10.4', '10.5', '10.6',
            '11.0', '11.1', '11.2', '11.3', '11.4',
        ],
        'tvOS' => [
            '9.0', '9.1', '9.2',
            '10.0', '10.1', '10.2',
            '11.0', '11.1', '11.2', '11.3', '11.4',
            '12.0', '12.1', '12.2', '12.3', '12.4',
            '13.0', '13.2', '13.3', '13.4',
            '14.0', '14.2', '14.3', '14.5', '14.6', '14.7',
            '15.0', '15.1', '15.2', '15.3', '15.4', '15.5', '15.6',
            '16.0', '16.1', '16.2', '16.3', '16.4', '16.5', '16.6',
            '17.0', '17.1', '17.2', '17.3', '17.4', '17.5', '17.6',
            '18.0', '18.1', '18.2', '18.3', '18.4',
        ],
        'iPadOS' => [
            '13.0', '13.1', '13.2', '13.3', '13.4', '13.5', '13.6', '13.7',
            '14.0', '14.1', '14.2', '14.3', '14.4', '14.5', '14.6', '14.7', '14.8',
            '15.0', '15.1', '15.2', '15.3', '15.4', '15.5', '15.6', '15.7', '15.8',
            '16.0', '16.1', '16.2', '16.3', '16.4', '16.5', '16.6', '16.7',
            '17.0', '17.1', '17.2', '17.3', '17.4', '17.5', '17.6', '17.7',
            '18.0', '18.1', '18.2', '18.3', '18.4',
        ],
    ];

    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $existing = $this->em->getRepository(OperatingSystem::class)->findAll();
        if (!empty($existing)) {
            $io->warning('Operating system table already populated. Skipping.');

            return Command::SUCCESS;
        }

        $count = 0;
        foreach (self::VERSIONS as $familyValue => $versions) {
            $family = OsFamily::from($familyValue);
            foreach ($versions as $version) {
                $os = new OperatingSystem();
                $os->setFamily($family);
                $os->setVersion($version);
                $this->em->persist($os);
                $count++;
            }
        }

        $this->em->flush();
        $io->success("Inserted $count OS versions.");

        return Command::SUCCESS;
    }
}
