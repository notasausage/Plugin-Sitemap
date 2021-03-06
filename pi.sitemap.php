<?php

class Plugin_sitemap extends Plugin {

	var $meta = array(
		'name'       => 'Sitemap',
		'version'    => '0.2',
		'author'     => 'Patrick Haney',
		'author_url' => 'http://patrickhaney.com'
	);

	public function __construct() {
		parent::__construct();
		$this->site_root = Statamic::get_site_root();
		$this->site_url = Statamic::get_site_url();
		$this->content_root = Statamic::get_content_root();
		$this->data = array();

		// Parses a maximum of max_entry_limit entries in a folder
		$this->max_entry_limit = 1000;
	} // END function __construct()

	public function index() {
		$output = false;
		$url = '/';

		// Add homepage to data
		$root_item = array('slug' => '/page','url'=>'');
		$this->parseFileItem($root_item);

		$this->parseTreeData( $url );    
		if( count( $this->content ) > 0 ) {
			$output = Parse::tagLoop( $this->content, $this->data );
		}

		return $output;
	} // END function index()


	/**
	 * Runs trough the navigation tree for as long as needed and adds items to $this->data.
	 * @param $url
	 */
	private function parseTreeData( $url ) {
		$url = Path::resolve( $url );
		$tree = Statamic::get_content_tree( $url, 1, $this->max_entry_limit, true, false, true, false, false );

		// Loop through document tree
		if( count( $tree ) > 0 ) {
			foreach( $tree as $item ) {
				if( $item['type'] == 'file' ) {
					$this->parseFileItem( $item, $url );
				}
				
				if( $item['type'] == 'folder' ) {
					$this->parseFolderItem( $item );
					$data = $this->getData( "page", $item['url'] );
					$priority = $this->setPriority( $data );
					
					// Don't parse children if folder priority is 0
					if( $item['has_children'] && $priority ) {
						$this->parseTreeData( $item['url'] );
					}
				
					// Don't parse entries if folder priority is 0
					if( $item['has_entries'] && $priority ) {
						$list = Statamic::get_content_list( $item['url'], $this->max_entry_limit, 0, false, true, 'date', 'desc' );
						foreach( $list as $entry ) {
							$this->parseEntryItem( $entry );
						}
					}
				}
			}
		}
	} // END function parseTreeData()


	/**
	 * This adds an item to the sitemap containing a folder (checks page.md)
	 * @param $item
	 **/
	private function parseFolderItem( $item ) {
		$data = $this->getData( "page", $item['url'] );    
		$permalink = Path::tidy( $this->site_url . '/' . $item['url'] );
		$moddate = array_key_exists( 'last_modified', $data ) ? date( "Y-m-d", $data['last_modified'] ) : date( "Y-m-d", strtotime("-1 day" ) );
		$priority = $this->setPriority($data);

		// Add this folder item if priority wasn't set to 0
		if( $priority ) {
			$this->data[] = array(
				'loc'        => $permalink,
				'lastmod'    => $moddate,
				'changefreq' => $this->setFrequency( $moddate ),
				'priority'   => $priority
			);
		}
	} // END function parseFolderItem()
  
	/**
	 * This adds an item to the sitemap containing a file
	 * @param $item
	 * @param $folder
	 **/
	private function parseFileItem( $item, $folder = null ) {
		$data = $this->getData( $item['slug'], $folder );
		$moddate = ( array_key_exists( 'last_modified', $data ) ) ? $data['last_modified'] : date( "Y-m-d", strtotime( "-1 day" ) );
		$permalink = Path::tidy( $this->site_url . '/' . $item['url'] );
		$priority = $this->setPriority($data);
		
		// Add this item if priority wasn't set to 0
		if( $priority ) {
			$this->data[] = array(
				'loc'        => $permalink,
				'lastmod'    => date( "Y-m-d", $moddate ),
				'changefreq' => $this->setFrequency( $moddate ),
				'priority'   => $priority
			);
		}
	} // END function parseFileItem()

	/**
	 * This adds an item to the sitemap containing an entry 
	 * @param $item
	 **/
	private function parseEntryItem( $item ) {
		$this->data[] = array(
			'loc'        => $item['permalink'],
			'lastmod'    => date( "Y-m-d", $item['last_modified'] ),
			'changefreq' => $this->setFrequency( $item['last_modified'] ),
			'priority'   => $this->setPriority( $item )
		);
	} // END function parseEntryItem()

	/**
	 * This returns the change frequency based on last modification date.
	 * @param $timestamp 
	 * @return string
	 **/
	private function setFrequency( $timestamp ) {
		if( $timestamp === false ) {
			return 'never';
		} elseif( $timestamp <= strtotime( '-1 year' ) ) {      
			return 'yearly';
		} elseif( $timestamp <= strtotime( '-1 month' ) ) {
			return 'monthly';
		} elseif( $timestamp <= strtotime( '-1 week' ) ) {
			return 'weekly';
		} elseif( $timestamp <= strtotime( '-1 day' ) ) {
			return 'daily';
		} else {
			return 'hourly';
		}
	} // END function setFrequency()

	/**
	 * This returns the sitemap priority of the item.
	 * @param $item 
	 * @return float
	 **/
	private function setPriority( $item ) {
		if( array_key_exists( 'priority', $item ) ) {
			return $item['priority'];
		} else {
			return 0.5;
		}
	} // END function setPriority()
	
	/**
	 * This returns the data for a folder, page or entry.
	 * @param $slug
	 * @param $folder
	 * @return array
	 **/
	private function getData( $slug, $folder = null ) {
		return Statamic::get_content_meta( $slug, $folder );
	} // END function getData()
} // END class Plugin_sitemap