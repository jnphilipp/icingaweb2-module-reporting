<?php
// Icinga Reporting | (c) 2018 Icinga GmbH | GPLv2

namespace Icinga\Module\Reporting;

use ipl\Html\HtmlDocument;
use ipl\Html\HtmlString;
use ipl\Sql;

class Template
{
    use Database;

    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $author;

    /** @var string */
    protected $title;

    /** @var string */
    protected $companyLogo;

    protected $timeframe;

    protected $schedule;

    /**
     * @param   int $id
     *
     * @return  static
     *
     * @throws  \Exception
     */
    public static function fromDb($id)
    {
        $template = new static();

        $db = $template->getDb();

        $select = (new Sql\Select())
            ->from('template')
            ->columns('*')
            ->where(['id = ?' => $id]);

        $row = $db->select($select)->fetch();

        if ($row === false) {
            throw new \Exception('Template not found');
        }

        $template
            ->setId($row->id)
            ->setName($row->name)
            ->setAuthor($row->author)
            ->setTitle($row->title)
            ->setCompanyLogo($row->company_logo);

       /* $select = (new Sql\Select())
            ->from('reportlet')
            ->columns('*')
            ->where(['report_id = ?' => $id]);

        $row = $db->select($select)->fetch();

        if ($row === false) {
            throw new \Exception('No reportlets configured.');
        }

        $reportlet = new Reportlet();

        $reportlet
            ->setId($row->id)
            ->setClass($row->class);

        $select = (new Sql\Select())
            ->from('config')
            ->columns('*')
            ->where(['reportlet_id = ?' => $row->id]);

        $rows = $db->select($select)->fetchAll();

        $config = [];

        foreach ($rows as $row) {
            $config[$row->name] = $row->value;
        }

        $reportlet->setConfig($config);

        $template->setReportlets([$reportlet]);

        $select = (new Sql\Select())
            ->from('schedule')
            ->columns('*')
            ->where(['report_id = ?' => $id]);

        $row = $db->select($select)->fetch();

        if ($row !== false) {
            $schedule = new Schedule();

            $schedule
                ->setId($row->id)
                ->setStart((new \DateTime())->setTimestamp((int) $row->start / 1000))
                ->setFrequency($row->frequency)
                ->setAction($row->action)
                ->setConfig(json_decode($row->config, true));

            $template->setSchedule($schedule);
        }
       */

        return $template;

    }

    /**
     * @return  int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param   int $id
     *
     * @return  $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param   string  $name
     *
     * @return  $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return  string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param   string  $author
     *
     * @return  $this
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param   string  $title
     *
     * @return  $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyLogo()
    {
        return $this->companyLogo;
    }

    /**
     * @param   string  $companyLogo
     *
     * @return  $this
     */
    public function setCompanyLogo($companyLogo)
    {
        $this->companyLogo = $companyLogo;

        return $this;
    }


    /**
     * @return  HtmlDocument
     */
    public function toHtml()
    {
        $html = new HtmlDocument();

        $template = new static();

        $db = $template->getDb();

        $dbtitle = (new Sql\Select())
            ->from('template')
            ->columns('title')
            ->where(['id = ?' => $this->id]);

        $rowtitle = $db->select($dbtitle)->fetch();


        $dbcompanylogo = (new Sql\Select())
            ->from('template')
            ->columns('company_logo')
            ->where(['id = ?' => $this->id]);

        $rowcompanylogo = $db->select($dbcompanylogo)->fetch();

        $string = new HtmlString("<!DOCTYPE html>
            <html>
            <head>
            <style>
         
            @media print 
            {
            @page { margin: 0; }
            body { margin:  0cm;}
            }
            
            p
            {
            margin:0;
            padding:0;
            font-size: 30px;
            }
            
            .undertitle
            {
            margin:0;
            padding:0;
            font-size: 18px;
            }
     
            .footer
            {
            width:180mm;
            margin-top:2mm;
            }
         
            .img_icinga
            {
            /*display: block;*/
            margin-left: auto;
            margin-right: auto;
            width: 45%;
            }
            
            .img_company_logo
            {
            margin-left: auto;
            margin-right: auto;
            width: 45%;
            padding: 10mm;
            
            }
            
            </style>
            </head>
            <body>
            <div id='allcontent'>
            <div id=\"wrapper\">
            
            <table class=\"heading\" style=\"width:100%;\" border='0'>
            <td rowspan=\"2\" valign=\"top\" align=\"left\" style=\"padding:10mm; width: 30%;\">Datum</td>
            <td rowspan=\"2\" valign=\"top\" align=\"middle\" style=\"padding:10mm; width: 30%;\"><img class='img_icinga' src=\"https://upload.wikimedia.org/wikipedia/de/thumb/7/70/Icinga_logo.svg/2880px-Icinga_logo.svg.png\"/></td>
            <td rowspan=\"2\" valign=\"top\" align=\"right\" style=\"padding:10mm; width: 30%;\">Spruch</td>
            </table>
            
            <p style='text-align:center; font-weight:bold; padding-top:20mm;'>$rowtitle->title</p>
            <br />
            <p class='undertitle' style=\"text-align:center; font-weight:bold; padding-top:5mm;\">Untertitel</p>

            <br>
           
            <div id='footer'>
            <table style=\"width:100%;\" border='0'>
            <td rowspan=\"2\" valign=\"top\" align=\"left\" style=\"padding:10mm; width: 30 %;\">Firmenname</td>
            <td rowspan=\"2\" valign=\"top\" align=\"middle\"><img class='img_company_logo' src=\"$rowcompanylogo->company_logo\"/></td>
            <td rowspan=\"2\" valign=\"top\" align=\"right\" style=\"padding:10mm; width: 30 %;\">Seitenzahl</td>
            </table>
            </div>
           
            </div>
            </div>
            </body>
            </html>");

        $html->setContent($string);

        //$html = new TemplateForm();

        return $html;
    }

}
