<?php
/**
 * portal-instructions.php
 *
 * @version 1.0
 * @date 7/19/16 6:54 AM
 * @package Wordpress Employee Portal
 */
?>
<div class="wrap">

	<h2>Employee Portal Settings</h2>

	<p>Configure notification options and preferences here.</p>

	<h2 class="nav-tab-wrapper">
		<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=portal-settings">Manage Portal Settings</a>
		<a class="nav-tab nav-tab-active" href="<?php echo admin_url() ?>admin.php?page=portal-settings&instructions">View Instructions</a>
	</h2>

	<h2>
		Available Shortcodes
	</h2>

	<p>This plugin includes shortcodes to display each available option individually by using the following shortcodes:</p>

	<p>
		To display a login form:<br />
		<code>[portal-login]</code>
	</p>

	<p>
		To display the current user's name:<br />
		<code>[portal-username]</code>
	</p>

	<p>
		To display a listing of schedule items:<br />
		<code>[portal-calendar heading=&quot;Optional Heading&quot; limit=&quot;Optional numeric count&quot;]</code>
	</p>

	<p>
		To display a listing of notes:<br />
		<code>[portal-notes heading=&quot;Optional Heading&quot; limit=&quot;Optional numeric count&quot; excerpt=&quot;0 or 1&quot;]</code>
		<br />
		To display the note content in a pop-up window, set the excerpt parameter == &quot;1&quot;, for example:<br />
		<code>[portal-notes heading=&quot;These are notes&quot; limit=&quot;12&quot; excerpt=&quot;1&quot;]</code>
	</p>

	<p>
		To display a listing of links:<br />
		<code>[portal-links heading=&quot;Optional Heading&quot; limit=&quot;Optional numeric count&quot;]</code>
	</p>

	<p>
		Display a single link (most recent, or by ID if provided):<br />
		<code>[portal-featured-link id=&quot;Optional numeric link ID&quot;]</code>
	</p>

	<p>
		To display a listing of downloads:<br />
		<code>[portal-downloads heading=&quot;Optional Heading&quot; limit=&quot;Optional numeric count&quot;]</code>
	</p>

	<h2>Customization Options</h2>

	<p>Available CSS Selectors:<br />
		<ul>
			<li>ul.portal-list</li>
			<li>ul.portal-list li</li>
			<li>#portal-loginform</li>
			<li>#portal-loginform p</li>
			<li>#portal-loginform p label</li>
			<li>#portal-loginform p input</li>
			<li>#portal-loginform p input[type=&quot;submit&quot;]</li>
			<li>#portal-loginform p input[type=&quot;submit&quot;]:hover</li>
			<li>#portal-loginform p input[type=&quot;submit&quot;]:active</li>
			<li>div.portal-button-wrapper</li>
			<li>a.portal-featured-link-button</li>
			<li>a.portal-featured-link-button:hover</li>
		</ul>
	</p>

	<h2>Notification Options</h2>

	<p>In the event that &quot;Enable Employee Notifications?&quot; is set to &quot;Yes&quot;, users with the user level &quot;employee&quot; will receive notifications for any of the selected event types, with individual user specificity applied.</p>

	<p>For example, if &quot;Schedules&quot; is selected, an employee would be notified of additions to their individual schedule, and schedule items without a specified employee would be sent to all employees.</p>

</div>
