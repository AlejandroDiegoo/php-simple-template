<h2>{{LIST_TITLE}}</h2>
{IF {{LIST_NAME}}}
	<ul>
		{FOR LIST_NAME}
			<li>{{LIST_NAME.ATTR_1}}</li>
		{END FOR}
	</ul>
{ELSE}
	{{LIST_ERROR}}
{END IF}