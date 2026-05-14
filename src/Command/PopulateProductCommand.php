<?php

namespace App\Command;

use App\Entity\OperatingSystem;
use App\Entity\Product;
use App\Entity\ProductType;
use App\Entity\User;
use App\Enum\OsFamily;
use App\Enum\SubmissionStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:populate-products',
    description: 'Seed the product table with iconic discontinued Apple products.',
)]
class PopulateProductCommand extends Command
{
    // [typeLabel, productName, technicalName, releaseDate, discontinuedYear, price, launchOS, lastSupportedOS, description]
    // launchOS / lastSupportedOS: [OsFamily value, version string] or null
    private const PRODUCTS = [
        // ── iPhone ──────────────────────────────────────────────────────────────
        [
            'type'             => 'iPhone',
            'productName'      => 'iPhone',
            'technicalName'    => 'iPhone (1st generation)',
            'releaseDate'      => '2007-06-29',
            'discontinuedYear' => '2008-07-11',
            'price'            => 499,
            'launchOS'         => ['iOS', '1.0'],
            'lastSupportedOS'  => ['iOS', '3.1'],
            'description'      => 'The phone that changed everything. Steve Jobs introduced it as "an iPod, a phone, and an internet communicator" rolled into one. Original 2G model, aluminum and glass, no App Store at launch.',
        ],
        [
            'type'             => 'iPhone',
            'productName'      => 'iPhone 3GS',
            'technicalName'    => 'iPhone 3GS',
            'releaseDate'      => '2009-06-19',
            'discontinuedYear' => '2012-09-12',
            'price'            => 199,
            'launchOS'         => ['iOS', '3.0'],
            'lastSupportedOS'  => ['iOS', '6.1'],
            'description'      => 'The "S" stood for speed. First iPhone with hardware-accelerated 3D graphics, video recording, voice control, and a 3MP autofocus camera. Remained on sale for three years — Apple\'s longest-running iPhone model at the time.',
        ],
        [
            'type'             => 'iPhone',
            'productName'      => 'iPhone SE (1st generation)',
            'technicalName'    => 'iPhone SE (1st generation)',
            'releaseDate'      => '2016-03-31',
            'discontinuedYear' => '2018-09-12',
            'price'            => 399,
            'launchOS'         => ['iOS', '9.3'],
            'lastSupportedOS'  => ['iOS', '15.8'],
            'description'      => 'Flagship A9 chip in the beloved 4-inch form factor. For everyone who refused to move on from the iPhone 5 design. One of the most powerful small phones ever made — and Apple discontinued it anyway.',
        ],

        // ── Mac ─────────────────────────────────────────────────────────────────
        [
            'type'             => 'MacBook Pro',
            'productName'      => 'MacBook Pro (15-inch, 2008)',
            'technicalName'    => 'MacBook Pro (15-inch, Early 2008)',
            'releaseDate'      => '2008-02-26',
            'discontinuedYear' => '2010-04-13',
            'price'            => 1999,
            'launchOS'         => ['macOS', '10.5 Leopard'],
            'lastSupportedOS'  => ['macOS', '10.11 El Capitan'],
            'description'      => 'The last PowerPC-compatible generation in spirit — first MacBook Pro with the penryn Core 2 Duo. Multitouch trackpad, LED-backlit display, and the iconic aluminum unibody that would define Mac design for a decade.',
        ],
        [
            'type'             => 'MacBook',
            'productName'      => 'MacBook (White, 2010)',
            'technicalName'    => 'MacBook (13-inch, Mid 2010)',
            'releaseDate'      => '2010-05-18',
            'discontinuedYear' => '2011-07-20',
            'price'            => 999,
            'launchOS'         => ['macOS', '10.6 Snow Leopard'],
            'lastSupportedOS'  => ['macOS', '10.13 High Sierra'],
            'description'      => 'The last white polycarbonate MacBook. Affordable, durable, and universally loved by students. When Apple discontinued it in favour of the all-aluminum lineup, a generation of Mac users mourned its passing.',
        ],
        [
            'type'             => 'MacBook',
            'productName'      => 'MacBook (12-inch)',
            'technicalName'    => 'MacBook (12-inch, 2015)',
            'releaseDate'      => '2015-04-10',
            'discontinuedYear' => '2019-07-09',
            'price'            => 1299,
            'launchOS'         => ['macOS', '10.10 Yosemite'],
            'lastSupportedOS'  => ['macOS', '12 Monterey'],
            'description'      => 'One USB-C port. One. The most controversial Mac Apple made in years — and also one of the thinnest, lightest laptops ever. Fanless Core M processor, Retina display, and a butterfly keyboard that divided opinion.',
        ],

        // ── iPad ────────────────────────────────────────────────────────────────
        [
            'type'             => 'iPad',
            'productName'      => 'iPad',
            'technicalName'    => 'iPad (1st generation)',
            'releaseDate'      => '2010-04-03',
            'discontinuedYear' => '2011-03-11',
            'price'            => 499,
            'launchOS'         => ['iOS', '3.2'],
            'lastSupportedOS'  => ['iOS', '5.1'],
            'description'      => '"What is it good for?" the critics asked. Within 80 days Apple sold three million. The original iPad defined the modern tablet, ran a custom iOS 3.2 build, and made a 9.7-inch IPS screen feel like magic in 2010.',
        ],
        [
            'type'             => 'iPad',
            'productName'      => 'iPad mini (1st generation)',
            'technicalName'    => 'iPad mini (1st generation)',
            'releaseDate'      => '2012-11-02',
            'discontinuedYear' => '2015-09-09',
            'price'            => 329,
            'launchOS'         => ['iOS', '6.0'],
            'lastSupportedOS'  => ['iOS', '9.3'],
            'description'      => 'The iPad nobody asked for and everyone bought. 7.9 inches, 53% lighter than the iPad 2. Proved that the tablet sweet spot was smaller than Apple originally thought — and led to four generations of the mini line.',
        ],
        [
            'type'             => 'iPad',
            'productName'      => 'iPad Air (1st generation)',
            'technicalName'    => 'iPad Air (1st generation)',
            'releaseDate'      => '2013-11-01',
            'discontinuedYear' => '2016-03-21',
            'price'            => 499,
            'launchOS'         => ['iOS', '7.0'],
            'lastSupportedOS'  => ['iOS', '12.4'],
            'description'      => '43% thinner than the iPad 4. The name said it all — impossibly light for a 9.7-inch tablet. A9X-class performance before the Pro existed. Set the visual template that every iPad Air and iPad Pro has followed since.',
        ],

        // ── Apple Watch ─────────────────────────────────────────────────────────
        [
            'type'             => 'Apple Watch',
            'productName'      => 'Apple Watch (1st generation)',
            'technicalName'    => 'Apple Watch (1st generation)',
            'releaseDate'      => '2015-04-24',
            'discontinuedYear' => '2016-09-07',
            'price'            => 349,
            'launchOS'         => ['watchOS', '1.0'],
            'lastSupportedOS'  => ['watchOS', '4.3'],
            'description'      => 'The one that started a category. Shipped in three collections — Sport, Watch, and Edition (up to $17,000 in 18-karat gold). Slow by modern standards but a genuine cultural moment. watchOS 1 required an iPhone for every app.',
        ],
        [
            'type'             => 'Apple Watch',
            'productName'      => 'Apple Watch Series 3',
            'technicalName'    => 'Apple Watch Series 3',
            'releaseDate'      => '2017-09-22',
            'discontinuedYear' => '2022-09-07',
            'price'            => 329,
            'launchOS'         => ['watchOS', '4.0'],
            'lastSupportedOS'  => ['watchOS', '8.7'],
            'description'      => 'The first Apple Watch with cellular. Five years on sale — Apple\'s longest-running Watch. The Series 3 was still being sold new until September 2022, making it a rare case of Apple keeping an old watch alive as an entry point.',
        ],
        [
            'type'             => 'Apple Watch',
            'productName'      => 'Apple Watch Series 6',
            'technicalName'    => 'Apple Watch Series 6',
            'releaseDate'      => '2020-09-18',
            'discontinuedYear' => '2021-10-18',
            'price'            => 399,
            'launchOS'         => ['watchOS', '7.0'],
            'lastSupportedOS'  => ['watchOS', '8.7'],
            'description'      => 'Blood oxygen sensor, always-on Retina display, and the fastest chip ever in a Watch at the time. Only sold for 13 months before being replaced by the Series 7. The fastest-discontinued Watch in the lineup\'s history.',
        ],

        // ── Apple TV ────────────────────────────────────────────────────────────
        [
            'type'             => 'Apple TV',
            'productName'      => 'Apple TV (1st generation)',
            'technicalName'    => 'Apple TV (1st generation)',
            'releaseDate'      => '2007-03-21',
            'discontinuedYear' => '2010-09-01',
            'price'            => 299,
            'launchOS'         => null,
            'lastSupportedOS'  => null,
            'description'      => 'Designed to stream content from iTunes to the TV. It had a hard drive (40 or 160 GB), ran a custom Linux-based OS, and required local sync rather than streaming from the cloud. Steve Jobs called it a "hobby" — and he wasn\'t wrong.',
        ],
        [
            'type'             => 'Apple TV',
            'productName'      => 'Apple TV (3rd generation)',
            'technicalName'    => 'Apple TV (3rd generation)',
            'releaseDate'      => '2012-03-07',
            'discontinuedYear' => '2015-10-29',
            'price'            => 99,
            'launchOS'         => null,
            'lastSupportedOS'  => null,
            'description'      => 'The puck that lived under millions of TVs. No App Store, no games, no Siri — just AirPlay, Netflix, and iTunes. At $99 it was an impulse buy. tvOS and the open App Store model arrived only with the 4th generation.',
        ],
        [
            'type'             => 'Apple TV',
            'productName'      => 'Apple TV HD',
            'technicalName'    => 'Apple TV HD (4th generation)',
            'releaseDate'      => '2015-10-30',
            'discontinuedYear' => '2022-11-04',
            'price'            => 149,
            'launchOS'         => ['tvOS', '9.0'],
            'lastSupportedOS'  => ['tvOS', '16.6'],
            'description'      => 'The Apple TV that finally got an App Store and Siri. tvOS 9 launched a new era for Apple\'s living room ambitions. Seven years on sale before being quietly discontinued in November 2022, replaced by a 2022 update to the same box.',
        ],

        // ── Accessoires ─────────────────────────────────────────────────────────
        [
            'type'             => 'iPod',
            'productName'      => 'iPod classic (7th generation)',
            'technicalName'    => 'iPod classic (7th generation)',
            'releaseDate'      => '2009-09-09',
            'discontinuedYear' => '2014-09-09',
            'price'            => 249,
            'launchOS'         => null,
            'lastSupportedOS'  => null,
            'description'      => 'The last iPod classic. 160 GB of music in your pocket. When Apple killed it in 2014 without announcement, fans quietly panicked and bought every remaining unit. The click wheel is now a design icon — and the iPod classic a collector\'s item.',
        ],
        [
            'type'             => 'AirPods',
            'productName'      => 'AirPods (1st generation)',
            'technicalName'    => 'AirPods (1st generation)',
            'releaseDate'      => '2016-12-13',
            'discontinuedYear' => '2019-03-20',
            'price'            => 159,
            'launchOS'         => null,
            'lastSupportedOS'  => null,
            'description'      => 'The earbuds everyone made fun of — until everyone had them. W1 chip, instant pairing, 5h battery. Launched alongside the iPhone 7 and the removal of the headphone jack. Defined a new product category and a new era of wireless audio.',
        ],
        [
            'type'             => 'HomePod',
            'productName'      => 'HomePod (1st generation)',
            'technicalName'    => 'HomePod (1st generation)',
            'releaseDate'      => '2018-02-09',
            'discontinuedYear' => '2023-01-15',
            'price'            => 349,
            'launchOS'         => null,
            'lastSupportedOS'  => null,
            'description'      => 'Audiophile sound in a fabric cylinder. Spatial audio, beamforming tweeters, and a subwoofer that defied its size. Apple discontinued it in early 2023 — just weeks before announcing the 2nd-generation HomePod. A beloved speaker that never found mass adoption.',
        ],

        // ── Other (Server, AirPort…) ────────────────────────────────────────────
        [
            'type'             => 'Accessory',
            'productName'      => 'AirPort Extreme',
            'technicalName'    => 'AirPort Extreme (6th generation, 802.11ac)',
            'releaseDate'      => '2013-06-11',
            'discontinuedYear' => '2018-04-26',
            'price'            => 199,
            'launchOS'         => null,
            'lastSupportedOS'  => null,
            'description'      => 'Apple\'s last and best Wi-Fi router. 802.11ac, 3×3 MIMO, Gigabit Ethernet, and a USB port for shared printers and drives. Apple killed the entire AirPort line in April 2018, leaving a hole in the smart home market it never filled.',
        ],
        [
            'type'             => 'Accessory',
            'productName'      => 'AirPort Time Capsule',
            'technicalName'    => 'AirPort Time Capsule (5th generation)',
            'releaseDate'      => '2013-06-11',
            'discontinuedYear' => '2018-04-26',
            'price'            => 299,
            'launchOS'         => null,
            'lastSupportedOS'  => null,
            'description'      => 'A Wi-Fi router with a 2 or 3 TB hard drive built in, for seamless Time Machine backups over the air. The most elegant backup solution Apple ever made. Discontinued the same day as the AirPort Extreme — quietly, without ceremony.',
        ],
        [
            'type'             => 'Mac Pro',
            'productName'      => 'Xserve',
            'technicalName'    => 'Xserve (Early 2009)',
            'releaseDate'      => '2009-03-03',
            'discontinuedYear' => '2011-01-31',
            'price'            => 2999,
            'launchOS'         => ['macOS', '10.5 Leopard'],
            'lastSupportedOS'  => ['macOS', '10.7 Lion'],
            'description'      => 'Apple\'s only rack-mounted server. Dual Xeon processors, up to 32 GB RAM, and Mac OS X Server out of the box. Used in studios, universities, and post-production houses. Discontinued in January 2011 — Apple told customers to buy Mac Pros or Mac minis instead.',
        ],
    ];

    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($this->em->getRepository(Product::class)->count([]) > 0) {
            $io->warning('Product table already has data. Skipping.');
            return Command::SUCCESS;
        }

        $user = $this->getOrCreateSeedUser();

        $osMap    = $this->buildOsMap();
        $typeMap  = $this->buildTypeMap();
        $inserted = 0;

        foreach (self::PRODUCTS as $data) {
            $type = $typeMap[$data['type']] ?? null;
            if (!$type) {
                $io->warning("Product type \"{$data['type']}\" not found — run app:populate-product-type first.");
                continue;
            }

            $product = new Product();
            $product->setProductName($data['productName']);
            $product->setTechnicalName($data['technicalName']);
            $product->setReleaseDate(new \DateTimeImmutable($data['releaseDate']));
            $product->setDiscontinuedYear(new \DateTimeImmutable($data['discontinuedYear']));
            $product->setOriginalPrice($data['price']);
            $product->setDescription($data['description']);
            $product->setProductType($type);
            $product->setModifiedByUser($user);
            $product->setStatus(SubmissionStatus::Approved);

            if ($data['launchOS']) {
                $os = $osMap[$data['launchOS'][0] . '|' . $data['launchOS'][1]] ?? null;
                if ($os) {
                    $product->setLaunchOS($os);
                }
            }

            if ($data['lastSupportedOS']) {
                $os = $osMap[$data['lastSupportedOS'][0] . '|' . $data['lastSupportedOS'][1]] ?? null;
                if ($os) {
                    $product->setLastSupportedOS($os);
                }
            }

            $this->em->persist($product);
            $inserted++;
        }

        $this->em->flush();
        $io->success("Inserted $inserted products.");

        return Command::SUCCESS;
    }

    private function getOrCreateSeedUser(): User
    {
        $user = $this->em->getRepository(User::class)->findOneBy([]);
        if ($user) {
            return $user;
        }

        $user = new User();
        $user->setUuid('seed-system-user-' . bin2hex(random_bytes(8)));
        $user->setRoles(['ROLE_ADMIN']);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /** @return array<string, OperatingSystem> keyed by "family|version" */
    private function buildOsMap(): array
    {
        $map = [];
        foreach ($this->em->getRepository(OperatingSystem::class)->findAll() as $os) {
            $map[$os->getFamily()->value . '|' . $os->getVersion()] = $os;
        }
        return $map;
    }

    /** @return array<string, ProductType> keyed by type name */
    private function buildTypeMap(): array
    {
        $map = [];
        foreach ($this->em->getRepository(ProductType::class)->findAll() as $type) {
            $map[$type->getType()] = $type;
        }
        return $map;
    }
}
