<?php

declare(strict_types=1);

namespace Renz;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\world\particle\FlameParticle;
use pocketmine\world\particle\EnchantmentTableParticle;
use pocketmine\world\particle\DustParticle;
use pocketmine\math\Vector3;
use pocketmine\world\World;
use pocketmine\color\Color;

class ParticleManager implements Listener {

    private PluginBase $plugin;
    private array $activeParticles = [];
    private array $rainbowParticles = [];

    public function __construct(PluginBase $plugin) {
        $this->plugin = $plugin;
        $this->plugin->getServer()->getPluginManager()->registerEvents($this, $this->plugin);
        $this->plugin->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
            foreach ($this->activeParticles as $playerName => $particleType) {
                $player = $this->plugin->getServer()->getPlayerByPrefix($playerName);
                if ($player !== null) {
                    $this->spawnParticle($player, $particleType);
                }
            }
        }), 5);
    }

    public function toggleParticle(Player $player, string $type): void {
        $name = $player->getName();
        if ($type === "rainbow") {
            if (isset($this->rainbowParticles[$name])) {
                unset($this->rainbowParticles[$name]);
                $player->sendMessage("Rainbow particle effect disabled.");
            } else {
                $this->rainbowParticles[$name] = true;
                $player->sendMessage("Rainbow particle effect enabled.");
            }
        } else {
            if (isset($this->activeParticles[$name])) {
                unset($this->activeParticles[$name]);
                $player->sendMessage("Particle effect disabled.");
            } else {
                $this->activeParticles[$name] = $type;
                $player->sendMessage("Particle effect enabled.");
            }
        }
    }

    private function spawnParticle(Player $player, string $type): void {
        $world = $player->getWorld();
        $position = $player->getPosition();

        switch ($type) {
            case "redwing":
                $this->spawnRedWingParticle($player, $position, $world);
                break;
            case "souloffire":
                $this->spawnSoulOfFireParticle($player, $position, $world);
                break;
            case "demon":
                $this->spawnDemonParticle($player, $position, $world);
                break;
        }
    }

    public function onPlayerMove(PlayerMoveEvent $event): void {
        $player = $event->getPlayer();
        if (isset($this->rainbowParticles[$player->getName()])) {
            $this->spawnRainbowParticle($player);
        }
    }

    public function spawnRainbowParticle(Player $player): void {
    $world = $player->getWorld();
    $position = $player->getPosition();

    $particles = [
        new DustParticle(new Color(255, 0, 0)),   // Merah
        new DustParticle(new Color(255, 165, 0)), // Oranye
        new DustParticle(new Color(255, 255, 0)), // Kuning
        new DustParticle(new Color(0, 255, 0)),   // Hijau
        new DustParticle(new Color(0, 0, 255)),   // Biru
        new DustParticle(new Color(75, 0, 130)),  // Nila
        new DustParticle(new Color(238, 130, 238)) // Ungu
    ];

    static $tick = 0;
    $tick++;

    foreach ($particles as $index => $particle) {
        $xOffset = ($index - 3) * 0.5;
        $zOffset = sin(deg2rad($tick * 10 + $index * 30)) * 0.5;
        $world->addParticle(new Vector3($position->x + $xOffset, $position->y + 0.5, $position->z + $zOffset), $particle);
    }
}

    public function spawnSoulOfFireParticle(Player $player, Vector3 $position, World $world): void {
    $enchantingParticle = new EnchantmentTableParticle();
    $flameParticle = new FlameParticle();

    $position = $player->getPosition();

    $headPosition = $position->add(0, 2.5, 0);
    for ($i = 0; $i < 360; $i += 30) {
        $radians = deg2rad($i);
        $x = cos($radians) * 0.5;
        $z = sin($radians) * 0.5;
        $world->addParticle($headPosition->add($x, 0, $z), $enchantingParticle);
    }

    $basePosition = $position->add(0, -0.5, 0);
    for ($i = 0; $i < 360; $i += 30) {
        $radians = deg2rad($i);
        $x = cos($radians) * 0.5;
        $z = sin($radians) * 0.5;
        $world->addParticle($basePosition->add($x, 0, $z), $flameParticle);
    }
}

    public function spawnRedWingParticle(Player $player, Vector3 $position, World $world): void {
        $redParticle = new DustParticle(new Color(255, 0, 0));
        $lightBlueParticle = new DustParticle(new Color(173, 216, 230));
        $darkBlueParticle = new DustParticle(new Color(0, 0, 139));

        $wingPattern = [
            // Sayap kiri
            [-0.5, 1, 0], [-1, 1.5, 0.2], [-1.5, 2, 0.4], [-2, 2.5, 0.6], [-2.5, 3, 0.8], [-3, 3.5, 1],
            [-3.5, 4, 1.2], [-4, 4.5, 1.4], [-4.5, 5, 1.6], [-5, 5.5, 1.8], [-5.5, 6, 2],
            // Sayap kanan
            [0.5, 1, 0], [1, 1.5, 0.2], [1.5, 2, 0.4], [2, 2.5, 0.6], [2.5, 3, 0.8], [3, 3.5, 1],
            [3.5, 4, 1.2], [4, 4.5, 1.4], [4.5, 5, 1.6], [5, 5.5, 1.8], [5.5, 6, 2],
            // Sayap kiri lapisan kedua
            [-0.5, 0.8, -0.2], [-1, 1.3, -0.2], [-1.5, 1.8, -0.2], [-2, 2.3, -0.2], [-2.5, 2.8, -0.2], [-3, 3.3, -0.2], [-3.5, 3.8, -0.2], [-4, 4.3, -0.2], [-4.5, 4.8, -0.2], [-5, 5.3, -0.2],
            // Sayap kanan lapisan kedua
            [0.5, 0.8, -0.2], [1, 1.3, -0.2], [1.5, 1.8, -0.2], [2, 2.3, -0.2], [2.5, 2.8, -0.2], [3, 3.3, -0.2], [3.5, 3.8, -0.2],
            [4, 4.3, -0.2], [4.5, 4.8, -0.2], [5, 5.3, -0.2],
        ];

        foreach ($wingPattern as $offset) {
            $world->addParticle($position->add($offset[0], $offset[1], $offset[2]), $redParticle);
        }

        for ($i = 0; $i < 360; $i += 15) {
            $radians = deg2rad($i);
            $x = cos($radians) * 1.0;
            $y = sin($radians) * 1.0;
            $particle = ($i % 30 == 0) ? $lightBlueParticle : $darkBlueParticle;
            $world->addParticle($position->add($x, $y + 3, 0), $particle);
        }
    }

   public function spawnDemonParticle(Player $player, Vector3 $position, World $world): void {
    // Colors for different parts of the demon
    $bodyColor = new DustParticle(new Color(128, 0, 128)); // Purple for the body
    $headColor = new DustParticle(new Color(128, 0, 128)); // Purple for the head
    $eyeColor = new DustParticle(new Color(255, 0, 0)); // Red for the eyes
    $hornColor = new DustParticle(new Color(255, 0, 0)); // Red for the horns

    $basePosition = $position->subtract(0, 0, 1.5);

    for ($y = 0; $y <= 3; $y += 0.5) {
        $world->addParticle($basePosition->add(0, $y, 0), $bodyColor);
    }

    $headPosition = $position->add(0, 2.5, -0.5);

    $world->addParticle($headPosition, $headColor); // Head
    $world->addParticle($headPosition->add(0.3, 0, 0.3), $eyeColor); // Right eye
    $world->addParticle($headPosition->add(-0.3, 0, 0.3), $eyeColor); // Left eye

    $hornPositions = [
        $headPosition->add(0.5, 0.5, 0),
        $headPosition->add(-0.5, 0.5, 0)
    ];
    foreach ($hornPositions as $hornPosition) {
        $world->addParticle($hornPosition, $hornColor);
    }

    $world->addParticle($headPosition->add(0.5, 1, 0), $hornColor);
    $world->addParticle($headPosition->add(-0.5, 1, 0), $hornColor);
   }
}