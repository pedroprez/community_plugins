<?php
/**
 * Update plugin release
 */

// Get variables
$access_id = (int) get_input('release_access_id');
$version = strip_tags(get_input('version'));
$release_notes = plugins_strip_tags(get_input('release_notes'));
$elgg_version = get_input('elgg_version');
$comments = get_input('comments', 'yes');
$recommended = get_input('recommended', 'no');
$guid = (int) get_input('release_guid');

// check permissions and existence of release
if (!($release = get_entity($guid)) ||
	!($project = get_entity($release->container_guid)) ||
	!($release instanceof PluginRelease) ||
	!$release->canEdit() ) {

	register_error('Unknown or insufficient access to release');
	forward($CONFIG->wwwroot . "pg/plugins/developer/" . get_loggedin_user()->username);
}

// save release entity info
$release->access_id = $access_id;
$release->version = $version;
$release->release_notes = $release_notes;
$release->comments = $comments;
$release->elgg_version = $elgg_version;

// update recommended if required
if ($recommended == 'yes') {
	$project->recommended_release_guid = $release->getGUID();
}

if ($release->save()) {
	system_message(elgg_echo("plugins:release:saved"));
} else {
	register_error(elgg_echo("plugins:error:uploadfailed"));
}

forward($release->getURL());
