<?php

	class Template {

		var $root = '';
		var $data = array();
		var $files = array();
		var $codeCompiled = array();
		var $codeUncompiled = array();

		/**/
		function Template($root, $labels) {
			/**/
			if ($root) {
				$this->root = $root;
			}
			/**/
			if ($labels) {
				$this->vars($labels);
			}	
		}

		/**/
		function vars($vars) {
			reset($vars);
			while (list($key, $value) = each($vars)) {
				$this->data['.'][0][$key] = $value;
			}
			return true;
		}

		/**/
		function blocks($name, $array) {
			/* Antes de guardar un bloque, lo actualizamos
			incluyendo el index del mismo */
			$index = isset($this->data[$name . '.']) ? sizeof($this->data[$name . '.']) : 0;
			$this->data[$name . '.'][] = array_merge(array(
				'index' => $index
			), $array);
			/* Después de añadir un array, actualizamos
			una variable general con el total de bloques */
			$this->vars(array(
				$name => sizeof($this->data[$name . '.'])
			));
			return true;
		}

		/**/
		function set($files) {
			reset($files);
			while(list($name, $path) = each($files)) {
				$this->files[$name] = $this->root . $path;
			}
			return true;
		}

		/**/
		function load($file) {
			$file = $this->files[$file];
			$fileCode = implode('', @file($file));
			return $this->codeUncompiled[$file] = $fileCode;
		}

		/**/
		function output($main, $regions) {

			$data = array(
				$main => $main
			);

			$write = array();


			while(list($regionFile, $regionLocation) = each($regions)) {

				$data[$regionFile] = $regionFile;
				$write[$regionFile] = $regionLocation;


			}

			$this->set($data);

			/* Cargamos el template principal */
			$mainCode = $this->load($main);
			/* Cargamos todas las regiones y las incluimos
			en el código del template principal */
			while(list($region, $regionLocation) = each($write)) {
				$regionCode = $this->load($region);
				$mainCode = str_replace($regionLocation, $regionCode, $mainCode);
			}
			/* Después de cargar las regiones, compilamos
			el código completo y lo ejecutamos */
			$mainCode = $this->compile($mainCode);
			$this->codeCompiled[$main] = $mainCode;
			eval($mainCode);
			return true;
		}

		/**/
		function destroy() {
			$this->data = array();
		}






























	
















	/**
	 * Compiles the given string of code, and returns
	 * the result in a string.
	 * If "do_not_echo" is true, the returned code will not be directly
	 * executable, but can be used as part of a variable assignment
	 * for use in assign_code_from_handle().
	 */
	function compile($code, $do_not_echo = false, $retvar = '')
	{


		// replace \ with \\ and then ' with \'.
		$code = str_replace('\\', '\\\\', $code);
		$code = str_replace('\'', '\\\'', $code);

		// change template varrefs into PHP varrefs



		// This one will handle varrefs WITH namespaces
		$varrefs = array();
		preg_match_all('#\{(([a-z0-9\-_]+?\.)+?)([a-z0-9\-_]+?)\}#is', $code, $varrefs);
		$varcount = sizeof($varrefs[1]);
		
		for ($i = 0; $i < $varcount; $i++)
		{
			$namespace = $varrefs[1][$i];
			$varname = $varrefs[3][$i];


			$new = $this->generate_block_varref($namespace, $varname);

			$code = str_replace($varrefs[0][$i], $new, $code);
		}



//$code = preg_replace('#\{{([a-z0-9\-_]*?)\}}#is', '( ( isset($this->data[\'.\'][0][\'\1\']) ) ? $this->data[\'.\'][0][\'\1\'] : \'\' )', $code);

		// This will handle the remaining root-level varrefs
		$code = preg_replace('#\{([a-z0-9\-_]*?)\}#is', '\' . ( ( isset($this->data[\'.\'][0][\'\1\']) ) ? $this->data[\'.\'][0][\'\1\'] : \'\' ) . \'', $code);


//print_r($code);
		// Break it up into lines.
		$code_lines = explode("\n", $code);

		$block_nesting_level = 0;
		$block_names = array();
		$block_names[0] = ".";

		// Second: prepend echo ', append ' . "\n"; to each line.
		$line_count = sizeof($code_lines);




		for ($i = 0; $i < $line_count; $i++)
		{
			$code_lines[$i] = chop($code_lines[$i]);







			if (preg_match('#{ IF (.*?) }#', $code_lines[$i], $m))
			{	


				$string = str_replace('\' . ( ( isset', '( ( isset', $m[1]);
				$string = str_replace(': \'\' ) . \'', ': \'\' )', $string);

				$code_lines[$i] = "\n" . 'if (' . $string . ')';
				$code_lines[$i] .= "\n" . '{';

			}
			else if (preg_match('#{ ELSE }#', $code_lines[$i], $m))
			{
				$code_lines[$i] = '} else {' . "\n";
			}

			else if (preg_match('#{ END IF }#', $code_lines[$i], $m))
			{
				$code_lines[$i] = '} // END ';
			}








			else if (preg_match('#{ FOR (.*?) }#', $code_lines[$i], $m))
			{
				$n[0] = $m[0];
				$n[1] = $m[1];

				// Added: dougk_ff7-Keeps templates from bombing if begin is on the same line as end.. I think. :)
				if ( preg_match('#{ END FOR (.*?) }>#', $code_lines[$i], $n) )
				{
					$block_nesting_level++;
					$block_names[$block_nesting_level] = $m[1];
					if ($block_nesting_level < 2)
					{
						// Block is not nested.
						$code_lines[$i] = '$_' . $n[1] . '_count = ( isset($this->data[\'' . $n[1] . '.\']) ) ?  sizeof($this->data[\'' . $n[1] . '.\']) : 0;';
						$code_lines[$i] .= "\n" . 'for ($_' . $n[1] . '_i = 0; $_' . $n[1] . '_i < $_' . $n[1] . '_count; $_' . $n[1] . '_i++)';
						$code_lines[$i] .= "\n" . '{';
					}
					else
					{
						// This block is nested.

						// Generate a namespace string for this block.
						$namespace = implode('.', $block_names);
						// strip leading period from root level..
						$namespace = substr($namespace, 2);
						// Get a reference to the data array for this block that depends on the
						// current indices of all parent blocks.
						$varref = $this->generate_block_data_ref($namespace, false);
						// Create the for loop code to iterate over this block.
						$code_lines[$i] = '$_' . $n[1] . '_count = ( isset(' . $varref . ') ) ? sizeof(' . $varref . ') : 0;';
						$code_lines[$i] .= "\n" . 'for ($_' . $n[1] . '_i = 0; $_' . $n[1] . '_i < $_' . $n[1] . '_count; $_' . $n[1] . '_i++)';
						$code_lines[$i] .= "\n" . '{';
					}

					// We have the end of a block.
					unset($block_names[$block_nesting_level]);
					$block_nesting_level--;
					$code_lines[$i] .= '} // END ' . $n[1];
					$m[0] = $n[0];
					$m[1] = $n[1];
				}
				else
				{
					// We have the start of a block.
					$block_nesting_level++;
					$block_names[$block_nesting_level] = $m[1];
					if ($block_nesting_level < 2)
					{
						// Block is not nested.
						$code_lines[$i] = '$_' . $m[1] . '_count = ( isset($this->data[\'' . $m[1] . '.\']) ) ? sizeof($this->data[\'' . $m[1] . '.\']) : 0;';
						$code_lines[$i] .= "\n" . 'for ($_' . $m[1] . '_i = 0; $_' . $m[1] . '_i < $_' . $m[1] . '_count; $_' . $m[1] . '_i++)';
						$code_lines[$i] .= "\n" . '{';
					}
					else
					{
						// This block is nested.

						// Generate a namespace string for this block.
						$namespace = implode('.', $block_names);
						// strip leading period from root level..
						$namespace = substr($namespace, 2);
						// Get a reference to the data array for this block that depends on the
						// current indices of all parent blocks.
						$varref = $this->generate_block_data_ref($namespace, false);
						// Create the for loop code to iterate over this block.
						$code_lines[$i] = '$_' . $m[1] . '_count = ( isset(' . $varref . ') ) ? sizeof(' . $varref . ') : 0;';
						$code_lines[$i] .= "\n" . 'for ($_' . $m[1] . '_i = 0; $_' . $m[1] . '_i < $_' . $m[1] . '_count; $_' . $m[1] . '_i++)';
						$code_lines[$i] .= "\n" . '{';
					}
				}
			}
			else if (preg_match('#{ END FOR (.*?) }#', $code_lines[$i], $m))
			{
				// We have the end of a block.
				unset($block_names[$block_nesting_level]);
				$block_nesting_level--;
				$code_lines[$i] = '} // END ' . $m[1];
			}





			else
			{
				// We have an ordinary line of code.
				if (!$do_not_echo)
				{
					$code_lines[$i] = 'echo \'' . $code_lines[$i] . '\' . "\\n";';
				}
				else
				{
					$code_lines[$i] = '$' . $retvar . '.= \'' . $code_lines[$i] . '\' . "\\n";'; 
				}
			}
		}

		// Bring it back into a single string of lines of code.
		$code = implode("\n", $code_lines);
		return $code	;

	}


	/**
	 * Generates a reference to the given variable inside the given (possibly nested)
	 * block namespace. This is a string of the form:
	 * ' . $this->data['parent'][$_parent_i]['$child1'][$_child1_i]['$child2'][$_child2_i]...['varname'] . '
	 * It's ready to be inserted into an "echo" line in one of the templates.
	 * NOTE: expects a trailing "." on the namespace.
	 */
	function generate_block_varref($namespace, $varname)
	{
		// Strip the trailing period.
		$namespace = substr($namespace, 0, strlen($namespace) - 1);

		// Get a reference to the data block for this namespace.
		$varref = $this->generate_block_data_ref($namespace, true);
		// Prepend the necessary code to stick this in an echo line.

		// Append the variable reference.
		$varref .= '[\'' . $varname . '\']';

		$varref = '\' . ( ( isset(' . $varref . ') ) ? ' . $varref . ' : \'\' ) . \'';

		return $varref;

	}


	/**
	 * Generates a reference to the array of data values for the given
	 * (possibly nested) block namespace. This is a string of the form:
	 * $this->data['parent'][$_parent_i]['$child1'][$_child1_i]['$child2'][$_child2_i]...['$childN']
	 *
	 * If $include_last_iterator is true, then [$_childN_i] will be appended to the form shown above.
	 * NOTE: does not expect a trailing "." on the blockname.
	 */
	function generate_block_data_ref($blockname, $include_last_iterator)
	{
		// Get an array of the blocks involved.
		$blocks = explode(".", $blockname);
		$blockcount = sizeof($blocks) - 1;
		$varref = '$this->data';
		// Build up the string with everything but the last child.
		for ($i = 0; $i < $blockcount; $i++)
		{
			$varref .= '[\'' . $blocks[$i] . '.\'][$_' . $blocks[$i] . '_i]';
		}
		// Add the block reference for the last child.
		$varref .= '[\'' . $blocks[$blockcount] . '.\']';
		// Add the iterator for the last child if requried.
		if ($include_last_iterator)
		{
			$varref .= '[$_' . $blocks[$blockcount] . '_i]';
		}

		return $varref;
	}










	


















	}

?>
