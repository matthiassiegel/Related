=== Related ===
Contributors: chipsandtv
Donate link: http://chipsandtv.com/
Tags: related posts, related, post
Requires at least: 2.9
Tested up to: 2.9.1
Stable tag: trunk

A simple 'related posts' plugin that lets you choose the related posts yourself instead of generating the list automatically.

== Description ==

A simple 'related posts' plugin that lets you choose the related posts yourself instead of generating the list automatically.

Features:

* Add related posts to your blog posts
* Choose the related posts yourself
* Custom markup possible, or simply use the default output
* Re-order related posts via drag and drop

I wrote the plugin for my own blog, where I wanted to have the option to add related posts to each blog post using a simple 
but functional plugin without a lot of junk. Unlike other 'related posts' plugins that use algorithms to automatically 
generate a list of posts, I wanted to be able to select the related posts myself.

The plugin targets at small and medium sized blogs. On blogs with lots of posts (500+) it might not be very comfortable to 
choose the posts with the select box.

At the moment I don't plan any improvements, but if you find the plugin useful and require a certain feature or 
improvement, just let me know.

To display the related posts, simply add

	<?php echo $related->show(get_the_ID()); ?>

to your template, inside the Wordpress loop.
For advanced options, see the installation docs.

== Installation ==

**Option 1 - Automatic install**

Use the plugin installer built into Wordpress to search for the plugin. Wordpress will then download and install it for you.

**Option 2 - Manual install**

1. Make sure the files are within a folder.
2. Copy the whole folder inside the wp-content/plugins/ folder.
3. In the backend, active the plugin. You can now select related posts when you create or edit blog posts.

**How to display the related posts on your website**

The related posts are displayed by adding

	<?php echo $related->show($post_id); ?>

to your template. Replace `` $post_id `` with a post ID. If you call it within the Wordpress loop, you can use

	<?php echo $related->show(get_the_ID()); ?>

You have the option of either outputting a pre-formatted list or returning a PHP array of related posts to customise the 
markup yourself.

**Examples**

*Example 1: Using the default output*

	<?php echo $related->show(get_the_ID()); ?>
	
This can be called within the Wordpress loop. It will output a `` <ul> `` list with links.

*Example 2: Returning an array*

	<?php $rel = $related->show(get_the_ID(), true); ?>
	
With the second argument set to true, it will return an array of post objects. Use it to generate your own custom markup. 
Here is an example:

	<?php
		$rel = $related->show(get_the_ID(), true);
	
		// Display the title of each related post
		foreach ($rel as $r) :
			echo $r->post_title . '<br />';
		endforeach;
	?>

== Frequently Asked Questions ==

= Who should use this plugin? =

People who want to list 'related posts' in their blog posts and want to choose the related posts themselves, instead of 
having a list generated automatically using algorithms like other plugins do.

= Where does the plugin store its data? =

Data is stored in the postmeta table in the Wordpress database. No additional tables are needed.

= How many related posts can I add? =

As many as you like, there's no limit.

= Wordpress version X.X is listed as minimum required version. Any chance it will work with earlier versions? =

There's a good chance it will work with earlier versions, but I haven't tested it.

= I have 500+ posts on my blog and selecting the posts with the one select box isn't very comfortable, lots of scrolling. =

That's true, it isn't ideal for large blogs. This might get improved in future releases, feel free to submit ideas.

== Screenshots ==

1. Choosing related posts in the edit post screen

== Changelog ==

= 1.0 =
* Initial release. No known issues.