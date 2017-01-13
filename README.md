# php-simple-template

Lorem Ipsum es simplemente el texto de relleno de las imprentas y archivos de texto. Lorem Ipsum ha sido el texto de relleno estándar de las industrias desde el año 1500, cuando un impresor (N. del T. persona que se dedica a la imprenta) desconocido usó una galería de textos y los mezcló de tal manera que logró hacer un libro de textos especimen.

### Require

    require_once('template.php');
    
### Assignment

Lorem Ipsum es simplemente el texto de relleno de las imprentas y archivos de texto. Lorem Ipsum ha sido el texto de relleno estándar de las industrias desde el año 1500.

    $template = new Template('my-templates-path', array(
        'PAGE_TITLE' => 'page title',
        'PAGE_DESCRIPTION' => 'page description'
    ));

### Vars

Lorem Ipsum es simplemente el texto de relleno de las imprentas y archivos de texto. Lorem Ipsum ha sido el texto de relleno estándar de las industrias desde el año 1500

    $template->vars(array(
        'LIST_TITLE' => 'list title',
        'LIST_ERROR' => 'list error'
    ));
    
### Blocks

Lorem Ipsum es simplemente el texto de relleno de las imprentas y archivos de texto. Lorem Ipsum ha sido el texto de relleno estándar de las industrias desde el año 1500, INDEX  

    for ($i = 1; $i <= 10; $i++) {
    
        $template->blocks('LIST_NAME', array(
            'ATTR_1' => 'list attr 1',
            'ATTR_2' => 'list attr 2'
        ));
        
    }

### Output

Lorem Ipsum es simplemente el texto de relleno de las imprentas y archivos de texto. Lorem Ipsum ha sido el texto de relleno estándar de las industrias desde el año 1500

    $template->output('main.tpl', array(
        'header.tpl' => '{HEADER_HERE}',
        'list.tpl' => '{LIST_HERE}'
    ));
    
### Template

Lorem Ipsum es simplemente el texto de relleno de las imprentas y archivos de texto. Lorem Ipsum ha sido el texto de relleno estándar de las industrias desde el año 1500
    
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <title>{PAGE_TITLE}</title>
            <meta charset="utf-8" />
            <meta http-equiv="content-type" content="text/html; charset=utf-8" />
            <meta name="description" content="{PAGE_DESCRIPTION}">
        </head>
        <body>
            <header>
                {HEADER_HERE}
            </header>
            <div id="content">
                {LIST_HERE}
            </div>
        </body>
    </html>

Lorem Ipsum es simplemente el texto de relleno de las imprentas y archivos de texto. Lorem Ipsum ha sido el texto de relleno estándar de las industrias desde el año 1500
    
    <h1>{LIST_TITLE}</h1>
    { IF {LIST_NAME} }
        <ul>
            { FOR LIST_NAME }
                <li>{LIST_NAME.ATTR_1}</li>
            { END FOR LIST_NAME }
        </ul>
    { ELSE }
        {LIST_ERROR}
    { END IF }
    
