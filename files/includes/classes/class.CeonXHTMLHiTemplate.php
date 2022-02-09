<?php

// {{{ class CeonXHTMLHiTemplate

/**
 * Class Provides Templating Functionality; Including replacing variables with source and enabling/
 * disabling optional sections in the source.
 *
 * Ported from Ceon Site Engine to Zen Cart (inc backporting to PHP4 from PHP5).
 *
 * @package     ceon_back_in_stock_notifications
 * @author      Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
 * @copyright   Copyright 2004-2012 Ceon
 * @link        http://dev.ceon.net/web/zen-cart/back-in-stock-notifications
 * @license     http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version     $Id: class.CeonXHTMLHiTemplate.php 904 2012-01-01 17:24:17Z conor $
 */
class CeonXHTMLHiTemplate
{
	// {{{ properties
	
	/**
	 * The XHTML source code for this location. Can be accessed and modified by external routines
	 * directly!
	 *
	 * @var     string
	 * @access  public
	 */
	var $xhtml_source = '';
	
	/**
	 * Information about the browser being used (including its name and rendering capabilities).
	 *
	 * @var     object(Browser)
	 * @access  protected
	 */
	var $_browser = null;
	
	// }}}

	
	// {{{ constructor()

	/**
	 * Create a new CeonXHTMLHiTemplate object.
	 * 
	 * @access  public
	 * @param   string   $file_name  The path of the file to open
	 * @return  object   A new CeonXHTMLHiTemplate object.
	 */
	function CeonXHTMLHiTemplate($file_name = '')
	{
		if ($file_name != '') {
			if (!$this->xhtml_source = @file_get_contents($file_name)) {
				print "Couldn't open template file: " . $file_name;
				//throw new Exception("Couldn't open template file: " . $file_name);
			}
		}
	}
	
	// }}}
	

	// {{{ setXHTMLSource()
	
	/**
	 * Set the XHTML source for the template.
	 * 
	 * @access  public
	 * @param   string    $source   The XHTML source for the template
	 * @return  none
	 */
	function setXHTMLSource($source)
	{
		$this->xhtml_source = $source;
	}
	
	// }}}
	
	
	// {{{ getXHTMLSource()
	
	/**
	 * Return the XHTML source code for the template
	 * 
	 * @access  public
	 * @return  reference(string)   A reference to the XHTML source for the template.
	 */
	function &getXHTMLSource()
	{
		return $this->xhtml_source;
	}
	
	// }}}
    
	
	// {{{ setBrowser()
	
	/**
	 * Sets the Browser being used to display/render the template.
	 * 
	 * @access  public
	 * @param   object(Browser)   $browser   The browser object.
	 * @return  none
	 */
	function setBrowser($browser)
	{
		$this->_browser =& $browser;
	}
	
	// }}}

	
	// {{{ appendSource()
	
	/**
	 * Simply concatenates the current instance's source with the supplied source.
	 *
	 * @access  public
	 * @param   string    $source   A reference to the source to add on to that of this instance.
	 * @return  none
	 */
	function appendSource(&$source)
	{
		// Append source to this instance's source
		$this->xhtml_source .= $source;
	}
	
	// }}}

	
	// {{{ setVariable()
	
	/**
	 * Replaces all occurances of the supplied variable with the supplied source.
	 *
	 * @access  public
	 * @param   string    $variable_name    The name of the variable to be replaced.
	 * @param   string    $source           A reference to the source to replace the variable with.
	 * @return  integer   The number of times the variable was set in the source (or a rough
	 *                    estimate of this value for PHP4).
	 */
	function setVariable($variable_name, $source)
	{
		$set_count = 0;
		$method_set_count = 0;
		
		// Replace bracketed replacement tags (e.g. {ceon:content})
		$bracketed_tag = '{ceon:' . $variable_name . '}';
		
		if (PHP_VERSION >= '5') {
			$this->xhtml_source = str_replace($bracketed_tag, $source, $this->xhtml_source,
				$method_set_count);
			
			$set_count += $method_set_count;
		} else {
			if (strpos($this->xhtml_source, $bracketed_tag) !== false) {
				$this->xhtml_source = str_replace($bracketed_tag, $source, $this->xhtml_source);
				
				$set_count += 1;
			}
		}
		
		// Enable <ceon:if blocks
		do {
			$updated_source = $this->extractElement($this->xhtml_source, 0, 'ceon:if',
				' isset="' . $variable_name . '"');
			
			if (!$updated_source) {
				break;
			}
			
			// Remove ceon:if tags from the source (thereby enabling the part)
			$if_block = $updated_source;
			
			$pattern = "|<ceon:if [^>]+>|sU";
			preg_match($pattern, $if_block, $match);
			
			$if_block_enabled = str_replace($match[0], '', $if_block);
			
			// Remove trailing '</ceon:if>'
			$if_block_enabled = substr($if_block_enabled, 0, strlen($if_block_enabled) - 10);
			
			// Finally, enable this if block
			$this->xhtml_source = str_replace($if_block, $if_block_enabled, $this->xhtml_source);
		} while(1);
		
		// Enable {ceon:if isset=".." blocks
		do {
			$updated_source = $this->extractElement($this->xhtml_source, 0, 'ceon:if',
				' isset="' . $variable_name . '"', true);
			
			if (!$updated_source) {
				break;
			}
			
			// Remove ceon:if tags from the source (thereby enabling the part)
			$if_block = $updated_source;
			
			$pattern = "|{ceon:if [^}]+}|sU";
			preg_match($pattern, $if_block, $match);
			
			$if_block_enabled = str_replace($match[0], '', $if_block);
			
			// Remove trailing '{/ceon:if}'
			$if_block_enabled = substr($if_block_enabled, 0, strlen($if_block_enabled) - 10);
			
			// Finally, enable this if block
			$this->xhtml_source = str_replace($if_block, $if_block_enabled, $this->xhtml_source);
		} while(1);
		
		// Enable {ceon:if isset='..' blocks
		do {
			$updated_source = $this->extractElement($this->xhtml_source, 0, 'ceon:if',
				" isset='" . $variable_name . "'", true);
			
			if (!$updated_source) {
				break;
			}
			
			// Remove ceon:if tags from the source (thereby enabling the part)
			$if_block = $updated_source;
			
			$pattern = "|{ceon:if [^}]+}|sU";
			preg_match($pattern, $if_block, $match);
			
			$if_block_enabled = str_replace($match[0], '', $if_block);
			
			// Remove trailing '{/ceon:if}'
			$if_block_enabled = substr($if_block_enabled, 0, strlen($if_block_enabled) - 10);
			
			// Finally, enable this if block
			$this->xhtml_source = str_replace($if_block, $if_block_enabled, $this->xhtml_source);
		} while(1);
		
		// Replace ceon:variable block type tags
		$pattern = "|<ceon:variable name=[\"']" . $variable_name . "[\"']>.*</ceon:variable>|sU"; 
		
		preg_match_all($pattern, $this->xhtml_source, $matches, PREG_SET_ORDER);
		
		$num_matches = count($matches);
		for ($i = 0; $i < $num_matches; $i++) {
			if (PHP_VERSION >= '5') {
				$this->xhtml_source = str_replace($matches[$i][0], $source, $this->xhtml_source,
					$method_set_count);
				
				$set_count += $method_set_count;
			} else {
				if (strpos($this->xhtml_source, $matches[$i][0]) !== false) {
					$this->xhtml_source = str_replace($matches[$i][0], $source,
						$this->xhtml_source);
					
					$set_count += 1;
				}
			}
		}
		
		// Replace ceon:variable inline type tags
		$pattern = "|<ceon:variable name=[\"']" . $variable_name . "[\"'][\s]*/>|sU";
		
		preg_match_all($pattern, $this->xhtml_source, $matches, PREG_SET_ORDER);
		
		$num_matches = count($matches);
		for ($i = 0; $i < $num_matches; $i++) {
			if (PHP_VERSION >= '5') {
				$this->xhtml_source = str_replace($matches[$i][0], $source, $this->xhtml_source,
					$method_set_count);
				
				$set_count += $method_set_count;
			} else {
				if (strpos($this->xhtml_source, $matches[$i][0]) !== false) {
					$this->xhtml_source = str_replace($matches[$i][0], $source,
						$this->xhtml_source);
					
					$set_count += 1;
				}
			}
		}
		
		return $set_count;
	}
	
	// }}}
	
	
	// {{{ customiseForBrowser()
	
	/**
	 * Removes all sections from template which don't apply to the current browser and activates
	 * any sections which do.
	 *
	 * @access  public
	 * @return  boolean   The status of the extraction, boolean true for success, false on failure.
	 */
	function customiseForBrowser()
	{
		if (empty($this->_browser)) {
			// Use current user's browser's settings
			if (isset($GLOBALS['ceon']) && isset($GLOBALS['ceon']['session']->browser)) {
				$this->_browser = $GLOBALS['ceon']['session']->browser;
			} else {
				return false;
			}
		}
		
		// Activate the sections for the current browser ///////////////////////////////////////////
		$browser_name = $this->_browser->getBrowser();
		
		// Remove ceon:if tags from the source (thereby enabling the section!)
		do {
			$updated_source = $this->extractElement($this->xhtml_source, 0, 'ceon:if',
				' browser="' . $browser_name . '"');
			
			if (!$updated_source) {
				break;
			}
			
			$if_block = $updated_source;
			
			$pattern = "|<ceon:if [^>]+>|sU";
			preg_match($pattern, $if_block, $match);
			
			$if_block_enabled = str_replace($match[0], '', $if_block);
			
			// Remove trailing '</ceon:if>'
			$if_block_enabled = substr($if_block_enabled, 0, strlen($if_block_enabled) - 10);
			
			// Finally, enable this if block
			$this->xhtml_source = str_replace($if_block, $if_block_enabled, $this->xhtml_source);
		} while (1);
		
		// Remove all sections which shouldn't be displayed for this browser ////
		do {
			$updated_source = $this->extractElement($this->xhtml_source, 0, 'ceon:if',
				' browser="!' . $browser_name . '"');
			
			if (!$updated_source) {
				break;
			}
			
			$if_block = $updated_source;
			
			$this->xhtml_source = str_replace($if_block, '', $this->xhtml_source);
		} while (1);
		
		
		// Activate all sections which statisfy the "anything but this browser"  ////
		do {
			$updated_source = $this->extractElement($this->xhtml_source, 0, 'ceon:if', ' browser="!');
			
			if (!$updated_source) {
				break;
			}
			
			// Remove ceon:if tags from the source (thereby enabling the section!)
			$if_block = $updated_source;
			
			$pattern = "|<ceon:if [^>]+>|sU";
			preg_match($pattern, $if_block, $match);
			
			$if_block_enabled = str_replace($match[0], '', $if_block);
			
			// Remove trailing '</ceon:if>'
			$if_block_enabled = substr($if_block_enabled, 0, strlen($if_block_enabled) - 10);
			
			// Finally, enable this if block
			$this->xhtml_source = str_replace($if_block, $if_block_enabled, $this->xhtml_source);
		} while (1);
		
		
		// Now remove all other browser sections ////
		do {
			$updated_source = $this->extractElement($this->xhtml_source, 0, 'ceon:if', ' browser="');
			
			if (!$updated_source) {
				break;
			}
			
			$if_block = $updated_source;
			
			$this->xhtml_source = str_replace($if_block, '', $this->xhtml_source);
		} while (1);
		
		
		// Activate sections for flash /////////////////////////////////////////////////////////////
		$browser_supports_flash = $this->_browser->hasFeature('flash');
	
		if ($browser_supports_flash) {
			// User's browser supports flash so enable all flash sections!
			
			// Remove ceon:if tags from the source (thereby enabling the section!)
			do {
				$updated_source = $this->extractElement($this->xhtml_source, 0, 'ceon:if',
					' flash="true"');
				
				if (!$updated_source) {
					break;
				}
				
				$if_block = $updated_source;
				
				$pattern = "|<ceon:if [^>]+>|sU";
				preg_match($pattern, $if_block, $match);
				
				$if_block_enabled = str_replace($match[0], '', $if_block);
				
				// Remove trailing '</ceon:if>'
				$if_block_enabled = substr($if_block_enabled, 0, strlen($if_block_enabled) - 10);
				
				// Finally, enable this if block
				$this->xhtml_source = str_replace($if_block, $if_block_enabled,
					$this->xhtml_source);
			} while (1);
			
			// Remove all non-flash fallback sections so they aren't displayed
			do {
				$updated_source = $this->extractElement($this->xhtml_source, 0, 'ceon:if',
					' flash="false"');
				
				if (!$updated_source) {
					break;
				}
				
				$if_block = $updated_source;
				
				$this->xhtml_source = str_replace($if_block, '', $this->xhtml_source);
			} while (1);
		} else {
			// User's browser doesn't have flash installed so disable all flash sections
			
			// Remove ceon:if tags from the source (thereby enabling the non-flash sections)
			do {
				$updated_source = $this->extractElement($this->xhtml_source, 0, 'ceon:if',
					' flash="false"');
				
				if (!$updated_source) {
					break;
				}
				
				$if_block = $updated_source;
				
				$pattern = "|<ceon:if [^>]+>|sU";
				preg_match($pattern, $if_block, $match);
				
				$if_block_enabled = str_replace($match[0], '', $if_block);
				
				// Remove trailing '</ceon:if>'
				$if_block_enabled = substr($if_block_enabled, 0, strlen($if_block_enabled) - 10);
				
				// Finally, enable this if block
				$this->xhtml_source = str_replace($if_block, $if_block_enabled,
					$this->xhtml_source);
			} while (1);
			
			// Remove all flash sections so they aren't displayed
			do {
				$updated_source = $this->extractElement($this->xhtml_source, 0, 'ceon:if',
					' flash="true"');
				
				if (!$updated_source) {
					break;
				}
				
				$if_block = $updated_source;
				
				$this->xhtml_source = str_replace($if_block, '', $this->xhtml_source);
			} while (1);
		}
		
		return true;
	}
	
	// }}}
    
	
	// {{{ cleanSource()
	
	/**
	 * Removes all Ceon comment tags, ignored sections, template definitions and remaining
	 * placements from the source.
	 *
	 * @access  public
	 * @return  none
	 */
	function cleanSource()
	{
		// Clear ceon template definition tags from source
		do {
			$updated_source = $this->extractElement($this->xhtml_source, 0, 'ceon:template', '');
			
			if (!$updated_source) {
				break;
			}
			
			$template_block = $updated_source;
			
			$this->xhtml_source = str_replace($template_block, '', $this->xhtml_source);
		} while (1);
		
		
		// Clear <ceon:if block type sections
		do {
			$updated_source = $this->extractElement($this->xhtml_source, 0, 'ceon:if', '');
			
			if (!$updated_source) {
				break;
			}
			
			$if_block = $updated_source;
			
			$this->xhtml_source = str_replace($if_block, '', $this->xhtml_source);
		} while (1);
		
		// Clear {ceon:if block type sections
		do {
			$updated_source = $this->extractElement($this->xhtml_source, 0, 'ceon:if', '', true);
			
			if (!$updated_source) {
				break;
			}
			
			$if_block = $updated_source;
			
			$this->xhtml_source = str_replace($if_block, '', $this->xhtml_source);
		} while (1);
		
		
		// Clear ceon ignore sections from source
		$pattern = "|<!-- ceon-begin-ignore -->.*<!-- ceon-end-ignore -->|sU"; 
		
		preg_match_all($pattern, $this->xhtml_source, $matches, PREG_SET_ORDER);
		
		$num_matches = count($matches);
		for ($i = 0; $i < $num_matches; $i++) {
			$this->xhtml_source = str_replace($matches[$i][0], '', $this->xhtml_source);
		}
		
		
		// Clear ceon:variable inline type tags
		$pattern = "|<ceon:variable name=[^/>]+/>|sU";
		
		preg_match_all($pattern, $this->xhtml_source, $matches, PREG_SET_ORDER);
		
		$num_matches = count($matches);
		for ($i = 0; $i < $num_matches; $i++) {
			$this->xhtml_source = str_replace($matches[$i][0], '', $this->xhtml_source);
		}
		
		
		// Clear ceon:variable block type sections
		$pattern = "|<ceon:variable name=[^\>]+.*</ceon:variable>|sU";
		
		preg_match_all($pattern, $this->xhtml_source, $matches, PREG_SET_ORDER);
		
		$num_matches = count($matches);
		for ($i = 0; $i < $num_matches; $i++) {
			$this->xhtml_source = str_replace($matches[$i][0], '', $this->xhtml_source);
		}
		
		// Clear any remaining bracketed placements from source
		$this->xhtml_source = preg_replace("|{ceon:[ a-zA-Z0-9_\-]+}|sU", "", $this->xhtml_source);
		
		
		// Clear ceon comment markers from source
		$this->xhtml_source = str_replace('<!-- ceon', '', $this->xhtml_source);
		$this->xhtml_source = str_replace('ceon -->', '', $this->xhtml_source);
	}
	
	// }}}


	// {{{ extractTemplate()
	
	/**
	 * Extracts a template from the current source, storing it in place of the source.
	 *
	 * @access  public
	 * @param   string    $template_name   The name of the template to be extracted from the 
	                                       source.
	 * @return  boolean   The status of the extraction, Boolean true for success, false on failure.
	 */
	function extractTemplate($template_name)
	{
		// First off, extract the template from the source ///////
		$template_source = $this->extractElement($this->xhtml_source, 0, 'ceon:template',
			' name="' . $template_name . '"');
		
		if (!$template_source) {
			// Either no source to search or no template found in source
			return false;
		}
		
		// Template extraction was successful, store in this instance and return status
		$this->setXHTMLSource($template_source);
		
		return true;
	}
	
	// }}}

	
	// {{{ extractTemplateParts()
	
	/**
	 * Extracts all template parts from the current source or the source passed to it.
	 *
	 * @access  public
	 * @param   string    $template   The source to be scanned for template parts. If none
	 *                                specified, template's own source is used.
	 * @param   boolean   $wipe_template_parts_afterwards   Whether or to wipe the template parts
	 *                                                      found from the source or to replace
	 *                                                      them with a ceon:variable tag with the
	 *                                                      same name as the part. 
	 * @return  mixed     The template parts found or boolean false on failure.
	 */
	function extractTemplateParts($template = '', $wipe_template_parts_afterwards = true)
	{
		// Variable holds the template parts extracted and a list of any parts which are embedded
		// inside other parts
		$template_parts = array('embedded_parts' => array());
		
		if ($template == '') {
			// Using source from this object instance
			// Get a reference to the template's source (for clarity)
			$template =& $this->getXHTMLSource();
		} else {
			// Using source from a previous template part
		}
		
		// Now extract all template parts from the source ///////
		// Variable holds status of extraction
		$extract_more_parts = true;
		
		// Variable maintains pointer to current place in source
		$current_source_pos = 0;
		
		while ($extract_more_parts) {
			// Variable holds source for current part
			$part_source = '';
			
			// Search for a part start tag
			$part_start_tag_pos = strpos($template, '<!-- ceon-begin-part ', $current_source_pos);
			
			if ($part_start_tag_pos === false) {
				// No more tags found, end extraction
				$extract_more_parts = false;
			} else {
				// Starting tag found so get this part's name
				$part_name = '';
				
				$current_source_pos = $part_start_tag_pos + 20; // 20 = strlen('<!-- ceon-begin-part')
				
				while ($template[$current_source_pos] != '>') {
					$part_name .= $template[$current_source_pos];
					$current_source_pos++;
				}
				
				// Now remove ending '--' from name
				$part_name = substr($part_name, 0, strlen($part_name) - 2);
				
				// Remove preceeding/prevailing whitespace
				$part_name = trim($part_name);
				
				// Advance pointer past matched '>'
				$current_source_pos++;
				
				// Now that the name of the part is known it is possible to get the position of the
				// part closing tag
				$part_end_tag_pos = strpos($template, '<!-- ceon-end-part ' . $part_name . ' -->',
					$current_source_pos);
				
				if ($part_end_tag_pos !== false) {
					// Extract source for this part
					$template_parts[$part_name] = substr($template, $current_source_pos,
						$part_end_tag_pos - $current_source_pos);
					
					// Now examine this part for any further template parts
					$sub_template_parts = $this->extractTemplateParts($template_parts[$part_name]);
					
					if ($sub_template_parts != false) {
						// More template parts were found within the current part, add them to the
						// main array of template parts
						
						foreach ($sub_template_parts as $sub_template_part_name =>
								$sub_template_part_source) {
							if ($sub_template_part_name != 'embedded_parts') {
								// Pass down the template part (merge with current parts)
								$template_parts[$sub_template_part_name] =
									$sub_template_part_source;
								
								// Record the name of this embedded template part
								$template_parts['embedded_parts'][] = $sub_template_part_name;
								
								// Replace the sub template part in the current template part with a
								// placement marker
								// Source may have changed if this subpart had sub-subparts so
								// find the start and end tags again for replacement
								$sub_part_start_tag_pos = strpos($template_parts[$part_name],
									"<!-- ceon-begin-part " . $sub_template_part_name . " -->");
								$sub_part_end_tag_pos = strpos($template_parts[$part_name],
									'<!-- ceon-end-part ' . $sub_template_part_name . ' -->');
								
								if ($sub_part_start_tag_pos !== false) {
									$sub_part = substr($template_parts[$part_name],
										$sub_part_start_tag_pos, $sub_part_end_tag_pos -
										$sub_part_start_tag_pos + strlen("<!-- ceon-end-part " .
										$sub_template_part_name . " -->"));
									
									$template_parts[$part_name] = str_replace($sub_part ,
										'<ceon:variable name="' . $sub_template_part_name . '" />',
										$template_parts[$part_name]);
								}
							} else {
								// Pass down the names of any sub-sub template parts!
								$template_parts['embedded_parts'] = array_merge(
									$template_parts['embedded_parts'], $sub_template_part_source);
							}
						}
					}
					
					// Any sub parts processed, wipe template part from source so search for next
					// part can take place
					$part_start_tag_pos = strpos($template, '<!-- ceon-begin-part ' . $part_name .
						' -->');
					
					$part_end_tag_pos = strpos($template, '<!-- ceon-end-part ' . $part_name .
						' -->');
					
					if ($part_start_tag_pos !== false) {
						$part = substr($template, $part_start_tag_pos , $part_end_tag_pos -
							$part_start_tag_pos + strlen('<!-- ceon-end-part ' . $part_name .
							' -->'));
						
						if ($wipe_template_parts_afterwards) {
							$template = str_replace($part , '', $template);
						} else {
							$template = str_replace($part , '<ceon:variable name="' . $part_name .
								'" />', $template);
						}
					} else {
						$extract_more_parts = false;
						printf("Couldn't find start tag for %s!\n", $part_name);
						//throw new Exception("Couldn't find start tag for $part_name");
					}
					
					// Reset pointer to start of the newly updated template source
					$current_source_pos = 0;
				} else {
					// Couldn't find closing tag: parse error!
					$extract_more_parts = false;
					printf("Couldn't find closing tag: parse error when finding closing tag for %s!\n", $part_name);
					// throw new UnclosedTagsException(sprintf("Couldn't find closing tag: parse error when finding closing tag for %s!\n", $part_name));
				}
			}
		}
		
		return $template_parts;
	}
	
	// }}}
	
	
	// {{{ extractElement()
	
	/**
	 * Extracts the source for an element. Takes encapsulated elements of the same type into
	 * consideration so that they form part of the source also.
	 *
	 * @access  public
	 * @author  Conor Kerr <zen-cart.back-in-stock-notifications@dev.ceon.net>
	 * @param   string    $source       A reference to the source in which to look for the element.
	 * @param   integer   $start_pos    The position within the source to begin looking for the
	 *                                  element.
	 * @param   string    $tag_name     The name of the tag for this element.
	 * @param   string    $attributes   An optional attribute string which wmay be used to identify
	 *                                  an element uniquely (rather than simply searching for the
	 *                                  element type).
	 * @param   boolean   $match_brackets   Whether the elements use '<>' or '{}' (true for
	 *                                      brackets).
	 * @return  string|boolean   The source of the element or false if not found.
	 */
	function extractElement($source, $start_pos, $tag_name, $attributes = '', $match_brackets = false)
	{
		// Build the first test for the start tag
		if (!$match_brackets) {
			$start_tag_with_attributes = '<' . $tag_name . $attributes;
			
			$start_tag = '<' . $tag_name;
			
			$end_tag = '</' . $tag_name . '>';
		} else {
			$start_tag_with_attributes = '{' . $tag_name . $attributes;
			
			$start_tag = '{' . $tag_name;
			
			$end_tag = '{/' . $tag_name . '}';
		}
		
		$start_tag_pos = strpos($source, $start_tag_with_attributes, $start_pos);
		
		if ($start_tag_pos === false) {
			// No matching tag found
			return false;
		}
		
		// Find ending tag for this element
		$tag_source = substr($source, $start_tag_pos, (strlen($source) - $start_tag_pos));
		
		$num_open_elements = 1;
		
		$current_start_pos = $start_tag_pos + 1; // Add 1 to ensure this tag isn't matched again
		
		do {
			$end_tag_pos = strpos($source, $end_tag, $current_start_pos);
			
			if ($end_tag_pos === false) {
				// Starting tag not closed in source - error, can't extract the element!
				return false;
			} else {
				$num_open_elements--;
			}
			
			// Check if any starting tags for similar elements exist within this element. If they do
			// then closing tag just found may belong to another element.
			do {
				$current_start_pos = strpos($source, $start_tag, $current_start_pos);
				
				if ($current_start_pos !== false && $current_start_pos < $end_tag_pos) {
					// Another element of the same type exists within this element. Must ensure its
					// closing tag is not taken as the closing tag for this element.
					$num_open_elements++;
					
					$current_start_pos++; // Add 1 to ensure this tag isn't matched again
				} else {
					// No (more) encapsulated elements found
					break;
				}
			} while (1);
			
			if ($num_open_elements == 0) {
				// No open encapsulated tags found so the source found IS the source for the element
				break;
			}
			
			// There are still some open encapsulated tags, need to move past their closing tags to
			// find the closing tag for the element we are interested in
			$current_start_pos = $end_tag_pos + 1;
		} while (1);
		
		$tag_source = substr($source, $start_tag_pos, ($end_tag_pos - $start_tag_pos) +
			strlen($end_tag));
		
		return $tag_source;
	}
	
	// }}}
}
	
// }}}

?>