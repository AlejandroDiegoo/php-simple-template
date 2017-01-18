<?php

	/*
	 * Simple Template for PHP
	 *
	 * The Template engine allows to keep the HTML code in some
	 * external files which are completely free of PHP code. This
	 * way, it's possible keep logical programming (PHP code)
	 * away from visual structure.
	 *
	 * @author Alejandro Diego (https://github.com/AlejandroDiegoo)
	 * @version 1.0.2
	 */

	class SimpleTemplate {

		private $root = '';

		private $data = array();

		public function SimpleTemplate($root, $labels = null) {

			if ($root) {
				$this->root = $root;
			}

			if ($labels) {
				$this->add($labels);
			}

		}

		public function add($labels, $block = null) {

			if ($block) {
				$this->blocks($block, $labels);
				return false;
			}

			while (list($key, $value) = each($labels)) {
				$this->data[strtoupper($key)] = $value;
			}

			return true;

		}

		private function blocks($block, $labels) {

			// Before saving a block, we update it
			// including the index of the same
			$index = isset($this->data[$block]) ? sizeof($this->data[$block]) : 0;

			$this->data[$block][] = array_merge(array(
				'INDEX' => $index
			), array_change_key_case($labels, CASE_UPPER));

			return true;

		}

		private function load($file) {

			// Get the template source
			$file = $this->root . $file;
			$fileCode = implode('', @file($file));

			return $fileCode;

		}

		public function render($mainFile, $regions = null) {

			// Main template load
			$mainCode = $this->load($mainFile);

			// We load all regions and include
			// them in the main template code
			$regions = (is_array($regions) ? $regions : array());
			while(list($regionFile, $regionLocation) = each($regions)) {
				$regionCode = $this->load($regionFile);
				$mainCode = str_replace($regionLocation, $regionCode, $mainCode);
			}

			// After loading the regions, we compiled
			// the complete code and we execute it
			eval($this->compile($mainCode));

			return true;

		}

		// Generates a reference to the given variable inside the given
		private function getVarReference($blockName = null, $varName = null, $iterator = false) {

			$varReference = '$this->data';
			// Add the block name
			$varReference .= ($blockName) ? '[\'' . $blockName . '\']' : '';
			// Add the iterator if requiried
			$varReference .= ($blockName && $iterator) ? '[$_' . $blockName . '_i]' : '';
			// Append the variable reference
			$varReference .= ($varName) ? '[\'' . $varName . '\']' : '';

			return $varReference;

		}

		private function replaceVars($code) {

			$varReferences = array();
			preg_match_all('#\{\{(([a-z0-9\-_]+)\.)?([a-z0-9\-_]+)\}\}#is', $code, $varReferences);

			for ($i = 0; $i < sizeof($varReferences[1]); $i++) {

				$varReference = $this->getVarReference($varReferences[2][$i], $varReferences[3][$i], true);

				$newCode = '\' . ( isset(' . $varReference . ') && is_array( (' . $varReference . ') ) ? ';
				$newCode .= 'sizeof(' . $varReference . ') : ( isset(' . $varReference . ') ) ? ';
				$newCode .= $varReference . ' : \'\' ) . \'';

				$code = str_replace($varReferences[0][$i], $newCode, $code);

			}

			return $code;

		}
		
		// Compiles the given string of code,
		// and return the result in a string
		private function compile($code) {

			// Replace \ with \\ and then ' with \'.
			$code = str_replace('\\', '\\\\', $code);
			$code = str_replace('\'', '\\\'', $code);
			$code = $this->replaceVars($code);

			// Break it up into lines
			$codeLines = explode("\n", $code);

			for ($i = 0; $i < sizeof($codeLines); $i++) {

				$codeLines[$i] = chop($codeLines[$i]);

				if (preg_match('#{IF (.*?)}#', $codeLines[$i], $m)) {

					$ifCondition = str_replace('\' . ( isset', '( isset', $m[1]);
					$ifCondition = str_replace(': \'\' ) . \'', ': \'\' )', $ifCondition);

					$codeLines[$i] = "\n" . 'if (' . $ifCondition . ')';
					$codeLines[$i] .= "\n" . '{';

				} else if (preg_match('#{ELSE}#', $codeLines[$i], $m)) {

					$codeLines[$i] = '} else {' . "\n";

				} else if (preg_match('#{END IF}#', $codeLines[$i], $m)) {

					$codeLines[$i] = '} // END IF';

				} else if (preg_match('#{FOR (.*?)}#', $codeLines[$i], $m)) {

					$codeLines[$i] = '$_' . $m[1] . '_count = ( isset($this->data[\'' . $m[1] . '\']) ) ? ';
					$codeLines[$i] .= 'sizeof($this->data[\'' . $m[1] . '\']) : 0;';
					$codeLines[$i] .= "\n" . 'for ($_' . $m[1] . '_i = 0; $_' . $m[1] . '_i < $_';
					$codeLines[$i] .= $m[1] . '_count; $_' . $m[1] . '_i++)';
					$codeLines[$i] .= "\n" . '{';

				} else if (preg_match('#{END FOR}#', $codeLines[$i], $m)) {

					// We have the end of a block
					$codeLines[$i] = '} // END FOR';

				} else {

					$codeLines[$i] = 'echo \'' . $codeLines[$i] . '\' . "\\n";';

				}

			}

			// Bring it back into a single string of lines of code
			$code = implode("\n", $codeLines);
			return $code;

		}

	}

?>