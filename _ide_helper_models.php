<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string|null $title_jp
 * @property string|null $synopsis
 * @property string|null $poster
 * @property string|null $type
 * @property int|null $genre_id
 * @property int|null $studio_id
 * @property int|null $source_id
 * @property int|null $status_id
 * @property int|null $season
 * @property int|null $year
 * @property string|null $broadcast_day
 * @property int|null $episode_total
 * @property int|null $episode_watched
 * @property int|null $episode_downloaded
 * @property string|null $myanimelist_url
 * @property float|null $myanimelist_score
 * @property \Illuminate\Support\Carbon|null $air_date
 * @property array<array-key, mixed>|null $attributes
 * @property int $is_hot
 * @property int $is_airing
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AnimeEpisode> $episode
 * @property-read int|null $episode_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Genre> $genre
 * @property-read int|null $genre_count
 * @property-read \App\Models\Source|null $source
 * @property-read \App\Models\Status|null $status
 * @property-read \App\Models\Studio|null $studio
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime whereAirDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime whereAttributes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime whereBroadcastDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime whereEpisodeDownloaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime whereEpisodeTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime whereEpisodeWatched($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime whereGenreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime whereIsAiring($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime whereIsHot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime whereMyanimelistScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime whereMyanimelistUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime wherePoster($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime whereSeason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime whereSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime whereStudioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime whereSynopsis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime whereTitleJp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Anime whereYear($value)
 */
	class Anime extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $anime_id
 * @property int $episode_number
 * @property string|null $title
 * @property int|null $stream_id
 * @property string|null $stream_url
 * @property string|null $video_url
 * @property string|null $subtitle_lang
 * @property string|null $notes
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Anime $anime
 * @property-read \App\Models\Stream|null $stream
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnimeEpisode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnimeEpisode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnimeEpisode query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnimeEpisode whereAnimeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnimeEpisode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnimeEpisode whereEpisodeNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnimeEpisode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnimeEpisode whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnimeEpisode whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnimeEpisode whereStreamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnimeEpisode whereStreamUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnimeEpisode whereSubtitleLang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnimeEpisode whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnimeEpisode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnimeEpisode whereVideoUrl($value)
 */
	class AnimeEpisode extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string|null $developer
 * @property string|null $version
 * @property int $status_id
 * @property string|null $description
 * @property int|null $rating
 * @property string|null $itch_io_url
 * @property string|null $socigames_url
 * @property string|null $cover_image
 * @property int|null $genre_id
 * @property string|null $last_updated_on_itch
 * @property string|null $last_played_version
 * @property string|null $vndb_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AvnGallery> $galleries
 * @property-read int|null $galleries_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Genre> $genres
 * @property-read int|null $genres_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AvnSave> $saves
 * @property-read int|null $saves_count
 * @property-read \App\Models\Status|null $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AvnWalkthrough> $walkthroughs
 * @property-read int|null $walkthroughs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avn newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avn newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avn onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avn query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avn whereCoverImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avn whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avn whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avn whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avn whereDeveloper($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avn whereGenreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avn whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avn whereItchIoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avn whereLastPlayedVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avn whereLastUpdatedOnItch($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avn whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avn whereSocigamesUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avn whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avn whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avn whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avn whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avn whereVndbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avn withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avn withoutTrashed()
 */
	class Avn extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $avn_id
 * @property string|null $image_url
 * @property string|null $description
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnGallery newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnGallery newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnGallery query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnGallery whereAvnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnGallery whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnGallery whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnGallery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnGallery whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnGallery whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnGallery whereUpdatedAt($value)
 */
	class AvnGallery extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $avn_id
 * @property string|null $file_url
 * @property string|null $file_path
 * @property string|null $label
 * @property string|null $version
 * @property string|null $description
 * @property int $sort
 * @property string|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnSave newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnSave newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnSave query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnSave whereAvnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnSave whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnSave whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnSave whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnSave whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnSave whereFileUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnSave whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnSave whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnSave whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnSave whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnSave whereVersion($value)
 */
	class AvnSave extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $avn_id
 * @property string|null $file_url
 * @property string|null $file_path
 * @property string|null $label
 * @property int $sort
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Avn|null $avn
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnWalkthrough newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnWalkthrough newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnWalkthrough query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnWalkthrough whereAvnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnWalkthrough whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnWalkthrough whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnWalkthrough whereFileUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnWalkthrough whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnWalkthrough whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnWalkthrough whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvnWalkthrough whereUpdatedAt($value)
 */
	class AvnWalkthrough extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title_en
 * @property string $title_zh
 * @property string|null $synopsis
 * @property array<array-key, mixed>|null $external_titles
 * @property int|null $season
 * @property int|null $episode_latest
 * @property int|null $episode_watched
 * @property int|null $episode_watched_seasonal
 * @property int|null $episode_total
 * @property int|null $episode_dl
 * @property string|null $dl_stream
 * @property string|null $local_download_path
 * @property int|null $status_id
 * @property int $is_airing
 * @property string|null $myanimelist
 * @property string|null $image_cover
 * @property string|null $mc_name
 * @property string|null $mc_wikia
 * @property int|null $studio_id
 * @property int|null $source_id
 * @property int $is_hot
 * @property int $trending_sort
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Episode> $episodes
 * @property-read int|null $episodes_count
 * @property-read \App\Models\Stream|null $primary_stream
 * @property-read \App\Models\Source|null $source
 * @property-read \App\Models\Status|null $status
 * @property-read \App\Models\Studio|null $studio
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereDlStream($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereEpisodeDl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereEpisodeLatest($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereEpisodeTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereEpisodeWatched($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereEpisodeWatchedSeasonal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereExternalTitles($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereImageCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereIsAiring($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereIsHot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereLocalDownloadPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereMcName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereMcWikia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereMyanimelist($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereSeason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereStudioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereSynopsis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereTitleEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereTitleZh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereTrendingSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donghua withoutTrashed()
 */
	class Donghua extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $donghua_id
 * @property string|null $title
 * @property int $episode_number
 * @property int|null $stream_id
 * @property string|null $stream_url
 * @property array<array-key, mixed>|null $video_source_url
 * @property string|null $notes
 * @property int $is_an_update
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Donghua|null $donghua
 * @property-read \App\Models\Stream|null $stream
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereDonghuaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereEpisodeNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereIsAnUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereStreamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereStreamUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Episode whereVideoSourceUrl($value)
 */
	class Episode extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Anime> $animes
 * @property-read int|null $animes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Avn> $avns
 * @property-read int|null $avns_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genre newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genre newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genre query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genre whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genre whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genre whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genre whereUpdatedAt($value)
 */
	class Genre extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string $url
 * @property string|null $version
 * @property string|null $cover_image
 * @property \Illuminate\Support\Carbon|null $release_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocigamesRss newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocigamesRss newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocigamesRss query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocigamesRss whereCoverImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocigamesRss whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocigamesRss whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocigamesRss whereReleaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocigamesRss whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocigamesRss whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocigamesRss whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocigamesRss whereVersion($value)
 */
	class SocigamesRss extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source withoutTrashed()
 */
	class Source extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $slug
 * @property string|null $text_color
 * @property string|null $bg_color
 * @property int $order
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereBgColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereTextColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status withoutTrashed()
 */
	class Status extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $label
 * @property string|null $homepage_url
 * @property string|null $logo
 * @property int $is_cover_image
 * @property bool $is_crawlable
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Episode> $episodes
 * @property-read int|null $episodes_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stream newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stream newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stream onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stream query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stream whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stream whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stream whereHomepageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stream whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stream whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stream whereIsCoverImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stream whereIsCrawlable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stream whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stream whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stream whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stream whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stream withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stream withoutTrashed()
 */
	class Stream extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $url
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Studio whereUrl($value)
 */
	class Studio extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $avatar
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $teams
 * @property-read int|null $teams_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, ?string $guard = null, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User team($teams, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, ?string $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTeam($teams)
 */
	class User extends \Eloquent implements \Filament\Models\Contracts\FilamentUser, \Filament\Models\Contracts\HasAvatar {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $name
 * @property string $value
 * @property string|null $group
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VarEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VarEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VarEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VarEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VarEntry whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VarEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VarEntry whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VarEntry whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VarEntry whereValue($value)
 */
	class VarEntry extends \Eloquent {}
}

