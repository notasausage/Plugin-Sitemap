Plugin-Sitemap for Statamic
=============

This add-on for the Statamic CMS automatically generates a sitemap XML file based on your website's content. The add-on was originally developed by Max Westen and it has been updated to work in Statamic 1.6+. The Statamic 1.4 branch has been removed as I have little interest in supporting older versions of the CMS.

For Max's repo, visit: https://github.com/mwesten/Plugin-Sitemap

# Installation

## Download or clone the files to your project
Download the entire project and add the contents of the archive to a new folder in your Statamic `_add-ons` folder named `sitemap`.

Or clone this project on your system:

```
cd yourproject/_add-ons
git clone git://github.com/notasausage/Plugin-Sitemap.git sitemap
```

## Copy the layout and template files
Copy the files from the `_add-ons/sitemap/layouts` folder to your `_themes/themename/layouts` folder.

Copy the files from the `_add-ons/sitemap/templates` folder to your `_themes/themename/templates` folder.

## Copy the sitemap-page
Copy the file `_sitemap.md` from the `_add-ons/sitemap/content` folder to your `_content` folder.

The sitemap will then be available by visiting the sitemap URL at `http://yourproject.com/sitemap`.

# Usage
By default, all non-hidden folders, pages & entries will be scanned and included in the sitemap XML file. This XML file will include the URL, last modification time, change frequency and priority of each folder, page & entry found in the Statamic site.

For example, the site's homepage would appear in the XML file like so:

```
<url>
	<loc>http://yourproject.com/</loc>
	<lastmod>2013-10-08</lastmod>
	<changefreq>daily</changefreq>
	<priority>0.5</priority>
</url>
```

The page's URL is generated automatically. The last modification time (`lastmod`) comes from Statamic's own Publish Date for that item and is used to determine the change frequency (`changefreq`) of the URL. The importance of the page, or `priority` (defined in the Sitemaps XML protocol), can be any number from 0 to 1 (where 0 is not important, and 1 is very important) and is set to 0.5 by default.

# Custom priorities
If you want to change the priority for a folder, page or entry, you can set the priority on a case-by-case basis using the YAML prematter of the markdown file for that item.

For example, to give the homepage of your site a priority of 0.8, edit your `_content/page.md` file and add `priority: 0.8` to the YAML prematter like so:

```
---
title: Home
_fieldset: home
_template: home
_layout: home
priority: 0.8
---
This is my homepage content.
```

# Disclaimer
At this point this add-on has been modified from its original version to include some extra functionality. Hat tip to Max Westen for creating it in the first place. If you find any bugs or decide you want to contribute to the code, fork the project in Github and share your changes by initiating pull requests. Thanks!