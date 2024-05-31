<?php

declare(strict_types=1);

namespace Renz;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use jojoe77777\FormAPI\SimpleForm;

class Main extends PluginBase implements Listener {

    private ParticleManager $particleManager;

    public function onEnable(): void {
        $this->particleManager = new ParticleManager($this);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($sender instanceof Player) {
            $this->sendParticlesForm($sender);
            return true;
        } else {
            $sender->sendMessage("This command can only be used in-game.");
            return false;
        }
    }

    public function sendParticlesForm(Player $player): void {
        $form = new SimpleForm(function (Player $player, ?int $data) {
            if ($data !== null) {
                switch ($data) {
                    case 0:
                        $this->particleManager->toggleParticle($player, "rainbow");
                        break;
                    case 1:
                        $this->particleManager->toggleParticle($player, "redwing");
                        break;
                    case 2:
                        $this->particleManager->toggleParticle($player, "souloffire");
                        break;
                    case 3:
                        $this->particleManager->toggleParticle($player, "demon");
                        break;
                }
            }
        });

        $form->setTitle("§l§6God Particle Menu");
        $form->setContent("§2Choose a particle effect §f:");

        $permissions = [
            "rainbow.god",
            "redwing.god",
            "souloffire.god",
            "demon.god"
        ];

        $particles = [
            "Rainbow Particle",
            "Red Wing Particle",
            "Soul of Fire Particle",
            "Demon Particle"
        ];

        foreach ($particles as $index => $particle) {
            if ($player->hasPermission($permissions[$index])) {
                $form->addButton($particle, 0, "textures/ui/check");
            } else {
                $form->addButton($particle, 0, "textures/ui/cancel");
            }
        }

        $player->sendForm($form);
    }
}
