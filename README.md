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

Add member to faction:
```php
/** @var Faction $faction */
/** @var Member $member */
$member->join($faction, Member::RANK_MEMBER);
```

Remove player from faction:
```php
/** @var Member $member */
$member->leave();
```

Claiming plots:
```php
// Let's assume that player touched ground with 'claiming' stick
/** @var Player $player */
/** @var UltraFactions $uf */
$member = $uf->getMemberManager()->getMember($player);
if(!$member) return false; // Player isn't in faction
if($uf->getPlotManager()->claimPlot($member->getFaction(), $player)){
    // Faction claimed plot on $player position
} else {
    // Failed to claim plot
}
```
