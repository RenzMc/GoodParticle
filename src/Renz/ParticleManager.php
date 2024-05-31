<?php

declare(strict_types=1);

namespace Renz;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
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
        new DustParticle(new Color(255, 0, 0)),   // Red
        new DustParticle(new Color(255, 165, 0)), // Orange
        new DustParticle(new Color(255, 255, 0)), // Yellow
        new DustParticle(new Color(0, 255, 0)),   // Green
        new DustParticle(new Color(0, 0, 255)),   // Blue
        new DustParticle(new Color(75, 0, 130)),  // Indigo
        new DustParticle(new Color(238, 130, 238)) // Violet
    ];

    foreach ($particles as $index => $particle) {
        $xOffset = ($index - 3) * 0.5;
        $world->addParticle(new Vector3($position->x + $xOffset, $position->y + 2, $position->z), $particle);
    }
}

    public function spawnRedWingParticle(Player $player, Vector3 $position, World $world): void {
    $red = new DustParticle(new Color(255, 0, 0));
    $blue = new DustParticle(new Color(0, 0, 255));
    $white = new DustParticle(new Color(255, 255, 255));

    $wingPattern = [
        [-0.5, 1, 0], [-1, 1.5, 0], [-1.5, 2, 0], [-2, 2.5, 0], [-2.5, 3, 0], [-3, 3.5, 0], [-3.5, 4, 0],
        [-4, 4.5, 0], [-4.5, 5, 0], [-5, 5.5, 0], [-5.5, 6, 0], [-6, 6.5, 0], [-6.5, 7, 0], [-7, 7.5, 0],
        [-7.5, 8, 0], [-8, 8.5, 0], [-8.5, 9, 0], [-9, 9.5, 0], [-9.5, 10, 0], [-10, 10.5, 0],
        [0.5, 1, 0], [1, 1.5, 0], [1.5, 2, 0], [2, 2.5, 0], [2.5, 3, 0], [3, 3.5, 0], [3.5, 4, 0],
        [4, 4.5, 0], [4.5, 5, 0], [5, 5.5, 0], [5.5, 6, 0], [6, 6.5, 0], [6.5, 7, 0], [7, 7.5, 0],
        [7.5, 8, 0], [8, 8.5, 0], [8.5, 9, 0], [9, 9.5, 0], [9.5, 10, 0], [10, 10.5, 0],
        
        [-1.5, 1.2, 0], [-2, 1.7, 0], [-2.5, 2.2, 0], [-3, 2.7, 0], [-3.5, 3.2, 0], [-4, 3.7, 0],
        [-4.5, 4.2, 0], [-5, 4.7, 0], [-5.5, 5.2, 0], [-6, 5.7, 0], [-6.5, 6.2, 0], [-7, 6.7, 0],
        [-7.5, 7.2, 0], [-8, 7.7, 0], [-8.5, 8.2, 0], [-9, 8.7, 0], [-9.5, 9.2, 0], [-10, 9.7, 0],
        [1.5, 1.2, 0], [2, 1.7, 0], [2.5, 2.2, 0], [3, 2.7, 0], [3.5, 3.2, 0], [4, 3.7, 0],
        [4.5, 4.2, 0], [5, 4.7, 0], [5.5, 5.2, 0], [6, 5.7, 0], [6.5, 6.2, 0], [7, 6.7, 0],
        [7.5, 7.2, 0], [8, 7.7, 0], [8.5, 8.2, 0], [9, 8.7, 0], [9.5, 9.2, 0], [10, 9.7, 0],
    ];

    foreach ($wingPattern as $offset) {
        $world->addParticle($position->add($offset[0], $offset[1], $offset[2]), $red);
    }

    for ($i = 0; $i < 360; $i += 10) {
        $radians = deg2rad($i);
        $x = cos($radians) * 0.5;
        $z = sin($radians) * 0.5;
        $world->addParticle($position->add($x, 1, $z), $blue);
        $world->addParticle($position->add($x / 2, 1.5, $z / 2), $white);
    }
}

    public function spawnSoulOfFireParticle(Player $player, Vector3 $position, World $world): void {
        $particles = [
            new DustParticle(new Color(255, 69, 0)),
            new DustParticle(new Color(255, 140, 0)),
            new DustParticle(new Color(0, 0, 255))
        ];

        for ($i = 0; $i < 360; $i += 15) {
            $radians = deg2rad($i);
            $x = cos($radians) * 0.5;
            $z = sin($radians) * 0.5;

            $world->addParticle($position->add($x, 1, $z), $particles[0]);
            $world->addParticle($position->add($x * 1.5, 1.5, $z * 1.5), $particles[1]);
            $world->addParticle($position->add($x * 2, 2, $z * 2), $particles[2]);
        }
    }

    public function spawnDemonParticle(Player $player, Vector3 $position, World $world): void {
        $black = new DustParticle(new Color(0, 0, 0));
        $white = new DustParticle(new Color(255, 255, 255));
        $red = new DustParticle(new Color(255, 0, 0));

        $horns = [
            new Vector3(-0.3, 2.5, -0.3),
            new Vector3(-0.3, 2.5, 0.3),
            new Vector3(0.3, 2.5, -0.3),
            new Vector3(0.3, 2.5, 0.3),
        ];

        foreach ($horns as $offset) {
            $world->addParticle($position->add($offset->x, $offset->y, $offset->z), $black);
        }

        $body = [
            new Vector3(0, 2, 0), new Vector3(0, 1.5, 0), new Vector3(0, 1, 0),
            new Vector3(0.5, 1, 0), new Vector3(-0.5, 1, 0), new Vector3(0.5, 0.5, 0),
            new Vector3(-0.5, 0.5, 0), new Vector3(0.5, 0, 0), new Vector3(-0.5, 0, 0)
        ];

        foreach ($body as $offset) {
            $world->addParticle($position->add($offset->x, $offset->y, $offset->z), $red);
        }

        for ($i = 0; $i < 360; $i += 30) {
            $radians = deg2rad($i);
            $x = cos($radians) * 1;
            $z = sin($radians) * 1;
            $world->addParticle($position->add($x, 1, $z), $black);
            $world->addParticle($position->add($x * 1.5, 1.5, $z * 1.5), $white);
            $world->addParticle($position->add($x * 2, 2, $z * 2), $red);
        }
    }
}
