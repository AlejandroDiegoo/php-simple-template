{PAGE_VAR}
{ IF {LIST} }
	<ul>
		{ FOR LIST }
			<li>{LIST.TITLE}</li>
		{ END FOR LIST }
	</ul>
{ ELSE }
	{LIST_ERROR}
{ END IF }
