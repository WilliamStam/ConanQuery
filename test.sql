Select
  quote(g.name) as GUILD,
  quote(c.char_name) as NAME,
  case c.rank WHEN '2' then 'Leader' WHEN '1' then 'Officer' WHEN '0' then 'Peon' ELSE c.rank END RANK,
  quote(c.id) as DBid, 'TeleportPlayer '||ap.x||' '||ap.y||' '||ap.z as LOCATION,
  datetime(c.lastTimeOnline, 'unixepoch') as LASTONLINE
from characters as c
  left outer join guilds as g on g.guildid = c.guild
  left outer join actor_position as ap on ap.id = c.id
where
  lastTimeOnline > strftime('%s', 'now', '-5 days')
order by
  g.name,
  c.rank desc,
  c.level desc,
  c.char_name;
