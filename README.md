# UltraFactions
Rich full Faction plugin for MineCraft:PE Server software PocketMine-MP

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/PrimusLV/UltraFactions?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

```
#InDEV
No releases yet.
```

### API Examples

Creating new Faction:
```php
// This is just an idea, this doesn't work yet
$build = new FactionBuilder();

$build->setName("Name")
  ->setDisplayName("DisplayName")
  ->setLeader($player) // Clan can not exist without a leader
  ->setHome($player->getLevel()->getSafeSpawn());
  
$build->build();
```

