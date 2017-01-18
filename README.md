# php-simple-template

The Template engine allows to keep the HTML code in some external files which are completely free of PHP code. This way, it's possible keep logical programming (PHP code) away from visual structure. **Simple Template** allows you to easily load your HTML code through a template and insert templates into others.

**Demo:** https://codeluxestudio.com/github/php-simple-template/demo 

### Require

    require_once('simple.template.php');

### Assignment

When assigning the class, we must indicate the path where our templates will be hosted. In addition we can indicate the first variables of our application.

    $template = new SimpleTemplate('templates/', array(
        'PAGE_TITLE' => 'php-simple-template',
        'PAGE_DESCRIPTION' => 'page description',
        'PAGE_LANGUAGE' => 'en'
    ));

### Vars

Although we have already declared some variables in the assignment, we will be able to add more at any time (always before rendering). These will be added to those already indicated. To show them in the templates, you only need to write **{{VAR_NAME}}** within your template.

    $template->add(array(
        'LIST_TITLE' => 'list title',
        'LIST_ERROR' => 'list error'
    ));

### Lists

To create lists, you will have to enter a list name after the variable array. To access this information in the template, you'll have to loop the list using **{FOR LIST_NAME}** and within **{{LIST_NAME.ATTR_NAME}}**. To get the total number of records in a list, you only have to put the name of the list **{{LIST_NAME}}**.

    for ($i = 1; $i <= 10; $i++) {

        $template->add(array(
            'ATTR_1' => 'item ' . $i . ' attr 1',
            'ATTR_2' => 'item ' . $i . ' attr 2'
        ), 'LIST_NAME');

    }

### Render

To render you will have to indicate a main template, and if you wish, also a collection of secondary templates with their position in the main template.

    $template->render('main.tpl', array(
        'header.tpl' => '{{HEADER_HERE}}',
        'list.tpl' => '{{LIST_HERE}}'
    ));

### Templates

In the main template you can define the entire HTML structure and insert other templates using the variables indicated in the rendering (**{{HEADER_HERE}}** and **{{LIST_HERE}}**).

    <!DOCTYPE html>
    <html lang="{{PAGE_LANGUAGE}}">
        <head>
            <title>{{PAGE_TITLE}}</title>
            <meta charset="utf-8" />
            <meta http-equiv="content-type" content="text/html; charset=utf-8" />
            <meta name="description" content="{{PAGE_DESCRIPTION}}">
        </head>
        <body>
            <header>
                {{HEADER_HERE}}
            </header>
            <div>
                {{LIST_HERE}}
            </div>
        </body>
    </html>

In addition to variable writing and using **{FOR LIST_NAME}** to navigate lists, you can use **{IF (var or condition)}** and **{ELSE}** to create conditions.

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
