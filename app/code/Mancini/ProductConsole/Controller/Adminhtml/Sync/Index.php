<?php
namespace Mancini\ProductConsole\Controller\Adminhtml\Sync;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\ImportExport\Controller\Adminhtml\ImportResult as ImportResultController;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Adapter as ImportAdapter;
use Mancini\ProductConsole\Model\ImportApiSource;



class Index extends ImportResultController
{
    /** @var Import */
    protected $import;

    /** @var ImportApiSource */
    protected $sourceImport;

    protected $jsonResultFactory;

     /**
     * @var \Mancini\Frog\Helper\ApiCall 
     */
    protected $_apiCallHelper;


     /**
     * @var \Magento\ImportExport\Model\Report\ReportProcessorInterface
     */
    protected $reportProcessor;

    /**
     * @var \Magento\ImportExport\Model\History
     */
    protected $historyModel;

    /**
     * @var \Magento\ImportExport\Helper\Report
     */
    protected $reportHelper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\ImportExport\Model\Report\ReportProcessorInterface $reportProcessor
     * @param \Magento\ImportExport\Model\History $historyModel
     * @param \Magento\ImportExport\Helper\Report $reportHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\ImportExport\Model\Report\ReportProcessorInterface $reportProcessor,
        \Magento\ImportExport\Model\History $historyModel,
        \Magento\ImportExport\Helper\Report $reportHelper,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Mancini\Frog\Helper\ApiCall $apiCallHelper
    ) {
        parent::__construct($context, $reportProcessor, $historyModel, $reportHelper);
        $this->reportProcessor = $reportProcessor;
        $this->historyModel = $historyModel;
        $this->reportHelper = $reportHelper;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->_apiCallHelper = $apiCallHelper;
    }

    /**
     * Validate uploaded files action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /*$data = ['status' => 'Success'];
        $params['item'] = 'MAN-DELIVERY';
        $response = $this->_apiCallHelper->inetProductSyncUp();

        $itemDetails = json_decode($response,true);
        $result = $this->jsonResultFactory->create();
        $result->setData($itemDetails);
        return $result;*/

        $jsonData = '{
            "errors": "",
            "items": [
                {
                    "changetype": "new",
                    "vendorlogo": "",
                    "sku": "MHF-B1475-74R",
                    "skuserial": "3315697",
                    "availabletosell": "3",
                    "availability": "In Stock",
                    "inventoryrec": {
                        "description": [
                            "kentwood standard rails california king white, magnussen"
                        ],
                        "vendorcode": "MHF-",
                        "vendorname": "Magnussen Home Furnishings",
                        "vendorcatalog": "B1475-74R",
                        "dept": "21",
                        "department": "21",
                        "departmentname": "BEDS-ADULT",
                        "status": "SPO",
                        "retail": "200.00",
                        "retail1": "200.00",
                        "retail2": "159.99",
                        "retail3": ".00",
                        "retail4": ".00",
                        "retail5": ".00",
                        "retail6": ".00",
                        "nowprice": "",
                        "colors": "2000",
                        "comments": "",
                        "nosell": "0",
                        "hideretail": "0",
                        "webdisplay": "1",
                        "rlflag": "0",
                        "shipping": "",
                        "keyword": "",
                        "weight": "62",
                        "styles": [],
                        "images": [
                            "3315697/3315697.jpg"
                        ],
                        "thumbnails": [
                            "3315697/3315697.jpg"
                        ],
                        "hdimages": [
                            "3315697/3315697.jpg"
                        ],
                        "documents": [
                            ""
                        ]
                    },
                    "factsrec": {
                        "shortdescription": [
                            "Rails"
                        ],
                        "collection": "Kentwood",
                        "warranty": "One year limited warranty",
                        "material": "Wood",
                        "woodcare": "",
                        "mckay": "",
                        "size": "3",
                        "length": "86",
                        "depth": "3",
                        "height": "14",
                        "shippingsize": "",
                        "shippinglength": "",
                        "shippingdepth": "",
                        "shippingheight": "",
                        "category": "9",
                        "fabricname": "",
                        "fabriccontent": "",
                        "frameconstruction": [
                            ""
                        ],
                        "finish": "White",
                        "leatherprotection": "",
                        "included": [
                            ""
                        ],
                        "consfeatures": [
                            "Select Asian hardwoods & veneers with painted white finish"
                        ],
                        "spoinfo": [
                            ""
                        ],
                        "additionalinfo": [
                            "This classic design is features a painted white finish. Notice the nickel pulls offset by the stunning finish. Refined in form, the Kentwood collection will add grace and tradition to your home."
                        ],
                        "type": "",
                        "brand": "",
                        "covermaterial": "",
                        "quiltinglayers": "",
                        "comfortlayers": "",
                        "supportsystem": "",
                        "style": "",
                        "customh": "",
                        "customi": "",
                        "customj": "",
                        "mattfirmness": "",
                        "mattfirmnesslevel": "",
                        "mattsize": "Cal King",
                        "upholstered": "",
                        "mattinbox": "",
                        "adjbasecomp": "",
                        "cooltech": "",
                        "handcraft": "",
                        "edgesupport": "",
                        "lowmotion": "",
                        "weightcapacity": "",
                        "foundationoptions": [],
                        "mattressprotectors": [],
                        "childskus": [],
                        "boughttogether": [],
                        "webcollections": [
                            {
                                "name": "Furniture",
                                "vendor": "",
                                "category": ""
                            }
                        ]
                    },
                    "vendorfacts": {
                        "vendormemo": [
                            "ACCTS REC TAMMY FREY 519-662-3040 EXT352"
                        ],
                        "costmemo": [
                            ""
                        ],
                        "servicememo": [
                            ""
                        ],
                        "spomemo": [
                            "THURSDAY"
                        ],
                        "notesmemo": [
                            "$700 MINIMUM FOR ORDER TO SHIP OUT"
                        ]
                    },
                    "groupdata": []
                },
                {
                    "changetype": "new",
                    "vendorlogo": "",
                    "sku": "RES-PER-E6M",
                    "skuserial": "4439901",
                    "availabletosell": "1",
                    "availability": "In Stock",
                    "inventoryrec": {
                        "description": [
                            "performance firm eastern king mattress, the rest bed"
                        ],
                        "vendorcode": "RES-",
                        "vendorname": "Responsive Surface Technology (dba) Rest.",
                        "vendorcatalog": "7350-25000-12",
                        "dept": "6",
                        "department": "6",
                        "departmentname": "2500-UP",
                        "status": "SPO",
                        "retail": "5599.00",
                        "retail1": "5599.00",
                        "retail2": "6599.00",
                        "retail3": ".00",
                        "retail4": ".00",
                        "retail5": ".00",
                        "retail6": ".00",
                        "nowprice": "",
                        "colors": "2000",
                        "comments": "",
                        "nosell": "0",
                        "hideretail": "0",
                        "webdisplay": "1",
                        "rlflag": "0",
                        "shipping": "",
                        "keyword": "",
                        "weight": "125",
                        "styles": [],
                        "images": [
                            "4439901/res-_mattress.jpg"
                        ],
                        "thumbnails": [
                            "4439901/res-_mattress.jpg"
                        ],
                        "hdimages": [
                            "4439901/res-_mattress.jpg"
                        ],
                        "documents": [
                            ""
                        ]
                    },
                    "factsrec": {
                        "shortdescription": [
                            ""
                        ],
                        "collection": "The ReST Bed",
                        "warranty": "10 Year",
                        "material": "",
                        "woodcare": "",
                        "mckay": "",
                        "size": "",
                        "length": "76",
                        "depth": "80",
                        "height": "14",
                        "shippingsize": "",
                        "shippinglength": "",
                        "shippingdepth": "",
                        "shippingheight": "",
                        "category": "9",
                        "fabricname": "",
                        "fabriccontent": "",
                        "frameconstruction": [
                            ""
                        ],
                        "finish": "",
                        "leatherprotection": "",
                        "included": [
                            ""
                        ],
                        "consfeatures": [
                            "Sleep Skin cover, 4 inch gel infused memory foam, Smart Sensor, 5-Zone Air chamber support system, Manual adjustments plus automatic real time adjustments"
                        ],
                        "spoinfo": [
                            ""
                        ],
                        "additionalinfo": [
                            "The ReST Bed is the only bed that allows for maximum adjustments and automatic real time response based on our patented smart bed technology"
                        ],
                        "type": "",
                        "brand": "",
                        "covermaterial": "",
                        "quiltinglayers": "",
                        "comfortlayers": "",
                        "supportsystem": "",
                        "style": "",
                        "customh": "",
                        "customi": "",
                        "customj": "",
                        "mattfirmness": "Firm",
                        "mattfirmnesslevel": "1",
                        "mattsize": "Eastern King",
                        "upholstered": "",
                        "mattinbox": "",
                        "adjbasecomp": "",
                        "cooltech": "",
                        "handcraft": "",
                        "edgesupport": "",
                        "lowmotion": "",
                        "weightcapacity": "",
                        "foundationoptions": [],
                        "mattressprotectors": [],
                        "childskus": [],
                        "boughttogether": [],
                        "webcollections": [
                            {
                                "name": "Mattresses",
                                "vendor": "",
                                "category": ""
                            },
                            {
                                "name": "Adjustable Mattresses",
                                "vendor": "",
                                "category": ""
                            }
                        ]
                    },
                    "vendorfacts": {
                        "vendormemo": [
                            "a/r rep Lloyd Sommers 404-671-9397 ext 102",
                            ""
                        ],
                        "costmemo": [
                            ""
                        ],
                        "servicememo": [
                            ""
                        ],
                        "spomemo": [
                            "Order - Recieve: Monday - Wednesday (7 business days)"
                        ],
                        "notesmemo": [
                            ""
                        ]
                    },
                    "groupdata": []
                },
                {
                    "changetype": "new",
                    "vendorlogo": "",
                    "sku": "BPB-HYP-COT-6-WHI",
                    "skuserial": "4172595",
                    "availabletosell": "0",
                    "availability": "06/17/21",
                    "inventoryrec": {
                        "description": [
                            "hyper-cotton california king sheet set white, bedgear performance bedding"
                        ],
                        "vendorcode": "BPB-",
                        "vendorname": "Bedgear Performance Bedding",
                        "vendorcatalog": "BGS21AWFW",
                        "dept": "42",
                        "department": "42",
                        "departmentname": "LINENS",
                        "status": "SPO",
                        "retail": "229.99",
                        "retail1": "229.99",
                        "retail2": "299.99",
                        "retail3": ".00",
                        "retail4": ".00",
                        "retail5": ".00",
                        "retail6": ".00",
                        "nowprice": "",
                        "colors": "2000",
                        "comments": "",
                        "nosell": "0",
                        "hideretail": "0",
                        "webdisplay": "1",
                        "rlflag": "0",
                        "shipping": "",
                        "keyword": "",
                        "weight": "6.36",
                        "styles": [],
                        "images": [
                            "4172595/hc_white.jpg"
                        ],
                        "thumbnails": [
                            "4172595/hc_white.jpg"
                        ],
                        "hdimages": [
                            "4172595/hc_white.jpg"
                        ],
                        "documents": [
                            ""
                        ]
                    },
                    "factsrec": {
                        "shortdescription": [
                            "Sheet"
                        ],
                        "collection": "Hyper-Cotton",
                        "warranty": "1 Year",
                        "material": "",
                        "woodcare": "",
                        "mckay": "",
                        "size": "",
                        "length": "84",
                        "depth": "",
                        "height": "72",
                        "shippingsize": "",
                        "shippinglength": "",
                        "shippingdepth": "",
                        "shippingheight": "",
                        "category": "7",
                        "fabricname": "",
                        "fabriccontent": "",
                        "frameconstruction": [
                            ""
                        ],
                        "finish": "",
                        "leatherprotection": "",
                        "included": [
                            ""
                        ],
                        "consfeatures": [
                            "Power Band 360, Moisture wicking Cotton fabric, Air-X ventilated panels"
                        ],
                        "spoinfo": [
                            ""
                        ],
                        "additionalinfo": [
                            "Our Hyper-Cotton Performance sheets feature a quick dry breathable fabric that provides fast evaporation and continous air circulation to keep you cool and comfortable. Hyper-Cotton sheets are made with Eco friendly fibers that are soft, durable wrinkle resistant. The fitted sheet features our Powerband 360 to ensure a secure fit on all mattresses-perfect for power bases."
                        ],
                        "type": "Linen",
                        "brand": "",
                        "covermaterial": "",
                        "quiltinglayers": "",
                        "comfortlayers": "",
                        "supportsystem": "",
                        "style": "",
                        "customh": "",
                        "customi": "",
                        "customj": "",
                        "mattfirmness": "",
                        "mattfirmnesslevel": "",
                        "mattsize": "Cal King",
                        "upholstered": "",
                        "mattinbox": "",
                        "adjbasecomp": "",
                        "cooltech": "",
                        "handcraft": "",
                        "edgesupport": "",
                        "lowmotion": "",
                        "weightcapacity": "",
                        "foundationoptions": [],
                        "mattressprotectors": [],
                        "childskus": [],
                        "boughttogether": [],
                        "webcollections": [
                            {
                                "name": "Bedding",
                                "vendor": "",
                                "category": ""
                            }
                        ]
                    },
                    "vendorfacts": {
                        "vendormemo": [
                            "A/R REP JUSTINE DEROGATIS 631-414-7758 X 367 jderogatis@bedgear.com"
                        ],
                        "costmemo": [
                            ""
                        ],
                        "servicememo": [
                            ""
                        ],
                        "spomemo": [
                            "Tuesdays"
                        ],
                        "notesmemo": [
                            ""
                        ]
                    },
                    "groupdata": []
                }
                 ]
        }';

        $jsonData1 = '{
            "errors": "",
            "items": [
                {
                    "changetype": "new",
                    "vendorlogo": "",
                    "sku": "PAC-KQX-TWN-BDR-PM",
                    "skuserial": "4092121",
                    "availabletosell": "0",
                    "availability": "01/04/22",  
                    "inventoryrec": {
                        "description": [
                            "mainsail rails twin driftwood, pacific"
                        ],
                        "vendorcode": "PAC-",
                        "vendorname": "Pacific Mfg",
                        "vendorcatalog": "KQX-TWN-BDR-PM",
                        "dept": "11",
                        "department": "11",
                        "departmentname": "BEDS-KIDS",
                        "status": "SPO",
                        "retail": "",
                        "retail1": "1749.99",
                        "retail2": "1979.99",
                        "retail3": "",
                        "retail4": "",
                        "retail5": "",
                        "retail6": "",
                        "nowprice": "",
                        "colors": "",
                        "comments": "",
                        "nosell": "1",
                        "hideretail": "1",
                        "webdisplay": "1",
                        "rlflag": "0",
                        "shipping": "",
                        "keyword": "",
                        "weight": "",
                        "styles": [],
                        "images": [
                            ""
                        ],
                        "thumbnails": [
                            ""
                        ],
                        "hdimages": [
                            ""
                        ],
                        "documents": []
                    },
                    "factsrec": {
                        "shortdescription": [
                            "Rocker Recliner test123"
                        ],
                        "collection": "Maddox test123",
                        "warranty": "",
                        "material": "",
                        "woodcare": "",
                        "mckay": "",
                        "size": "",
                        "length": "",
                        "depth": "",
                        "height": "",
                        "shippingsize": "",
                        "shippinglength": "",
                        "shippingdepth": "",
                        "shippingheight": "",
                        "category": "",
                        "fabricname": "",
                        "fabriccontent": "",
                        "frameconstruction": [
                            ""
                        ],
                        "finish": "",
                        "leatherprotection": "",
                        "included": [
                            ""
                        ],
                        "consfeatures": [
                            ""
                        ],
                        "spoinfo": [
                            ""
                        ],
                        "additionalinfo": [
                            ""
                        ],
                        "type": "",
                        "brand": "Best Home Furnishings test123",
                        "covermaterial": "",
                        "quiltinglayers": "",
                        "comfortlayers": "",
                        "supportsystem": "",
                        "style": "",
                        "customh": "",
                        "customi": "",
                        "customj": "",
                        "mattfirmness": "matt firmness value",
                        "mattfirmnesslevel": "matt level value",
                        "mattsize": "matt size",
                        "upholstered": "",
                        "mattinbox": "",
                        "adjbasecomp": "",
                        "cooltech": "",
                        "handcraft": "",
                        "edgesupport": "",
                        "lowmotion": "",
                        "weightcapacity": "",
                        "foundationoptions": [],
                        "mattressprotectors": [],
                        "childskus": [],
                        "boughttogether": [],
                        "webcollections": [{
                            "name": "Furniture",
                            "vendor": "",
                            "category": ""
                        },
                        {
                            "name": "Misc.",
                            "vendor": "",
                            "category": ""
                        }]
                    }
                },
				                {
                    "changetype": "new",
                    "vendorlogo": "",
                    "sku": "SAC-KQX-TWN-BDR-PM",
                    "skuserial": "3082226",
                    "availabletosell": "0",
                    "availability": "01/05/22",  
                    "inventoryrec": {
                        "description": [
                            "mainsail rails twin testwood, pacific"
                        ],
                        "vendorcode": "PAC-",
                        "vendorname": "Pacific Mfg",
                        "vendorcatalog": "KQX-TWN-BDR-PM",
                        "dept": "11",
                        "department": "11",
                        "departmentname": "BEDS-KIDS",
                        "status": "SPO",
                        "retail": "",
                        "retail1": "1749.99",
                        "retail2": "1979.99",
                        "retail3": "",
                        "retail4": "",
                        "retail5": "",
                        "retail6": "",
                        "nowprice": "",
                        "colors": "",
                        "comments": "",
                        "nosell": "1",
                        "hideretail": "1",
                        "webdisplay": "1",
                        "rlflag": "0",
                        "shipping": "",
                        "keyword": "",
                        "weight": "",
                        "styles": [],
                        "images": [
                            ""
                        ],
                        "thumbnails": [
                            ""
                        ],
                        "hdimages": [
                            ""
                        ],
                        "documents": []
                    },
                    "factsrec": {
                        "shortdescription": [
                            "Rocker Recliner test123"
                        ],
                        "collection": "Maddox test123",
                        "warranty": "",
                        "material": "",
                        "woodcare": "",
                        "mckay": "",
                        "size": "",
                        "length": "",
                        "depth": "",
                        "height": "",
                        "shippingsize": "",
                        "shippinglength": "",
                        "shippingdepth": "",
                        "shippingheight": "",
                        "category": "",
                        "fabricname": "",
                        "fabriccontent": "",
                        "frameconstruction": [
                            ""
                        ],
                        "finish": "",
                        "leatherprotection": "",
                        "included": [
                            ""
                        ],
                        "consfeatures": [
                            ""
                        ],
                        "spoinfo": [
                            ""
                        ],
                        "additionalinfo": [
                            ""
                        ],
                        "type": "",
                        "brand": "Best Home Furnishings test123",
                        "covermaterial": "",
                        "quiltinglayers": "",
                        "comfortlayers": "",
                        "supportsystem": "",
                        "style": "",
                        "customh": "",
                        "customi": "",
                        "customj": "",
                        "mattfirmness": "matt firmness value",
                        "mattfirmnesslevel": "matt level value",
                        "mattsize": "matt size",
                        "upholstered": "",
                        "mattinbox": "",
                        "adjbasecomp": "",
                        "cooltech": "",
                        "handcraft": "",
                        "edgesupport": "",
                        "lowmotion": "",
                        "weightcapacity": "",
                        "foundationoptions": [],
                        "mattressprotectors": [],
                        "childskus": [],
                        "boughttogether": [],
                        "webcollections": [{
                            "name": "Furniture",
                            "vendor": "",
                            "category": ""
                        },
                        {
                            "name": "Misc.",
                            "vendor": "",
                            "category": ""
                        }]
                    }
                },
                {
                    "changetype": "new",
                    "vendorlogo": "",
                    "sku": "vAC-KQX-TWN-BDR-PM",
                    "skuserial": "2547786",
                    "availabletosell": "0",
                    "availability": "01/04/22",  
                    "inventoryrec": {
                        "description": [
                            "mainsail rails twin driftwood, pacific"
                        ],
                        "vendorcode": "PAC-",
                        "vendorname": "Pacific Mfg",
                        "vendorcatalog": "KQX-TWN-BDR-PM",
                        "dept": "11",
                        "department": "11",
                        "departmentname": "BEDS-KIDS",
                        "status": "SPO",
                        "retail": "",
                        "retail1": "1749.99",
                        "retail2": "1979.99",
                        "retail3": "",
                        "retail4": "",
                        "retail5": "",
                        "retail6": "",
                        "nowprice": "",
                        "colors": "",
                        "comments": "",
                        "nosell": "1",
                        "hideretail": "1",
                        "webdisplay": "1",
                        "rlflag": "0",
                        "shipping": "",
                        "keyword": "",
                        "weight": "",
                        "styles": [],
                        "images": [
                            ""
                        ],
                        "thumbnails": [
                            ""
                        ],
                        "hdimages": [
                            ""
                        ],
                        "documents": []
                    },
                    "factsrec": {
                        "shortdescription": [
                            "Rocker Recliner test123"
                        ],
                        "collection": "Maddox test123",
                        "warranty": "",
                        "material": "",
                        "woodcare": "",
                        "mckay": "",
                        "size": "",
                        "length": "",
                        "depth": "",
                        "height": "",
                        "shippingsize": "",
                        "shippinglength": "",
                        "shippingdepth": "",
                        "shippingheight": "",
                        "category": "",
                        "fabricname": "",
                        "fabriccontent": "",
                        "frameconstruction": [
                            ""
                        ],
                        "finish": "",
                        "leatherprotection": "",
                        "included": [
                            ""
                        ],
                        "consfeatures": [
                            ""
                        ],
                        "spoinfo": [
                            ""
                        ],
                        "additionalinfo": [
                            ""
                        ],
                        "type": "",
                        "brand": "Best Home Furnishings test123",
                        "covermaterial": "",
                        "quiltinglayers": "",
                        "comfortlayers": "",
                        "supportsystem": "",
                        "style": "",
                        "customh": "",
                        "customi": "",
                        "customj": "",
                        "mattfirmness": "matt firmness value",
                        "mattfirmnesslevel": "matt level value",
                        "mattsize": "matt size",
                        "upholstered": "",
                        "mattinbox": "",
                        "adjbasecomp": "",
                        "cooltech": "",
                        "handcraft": "",
                        "edgesupport": "",
                        "lowmotion": "",
                        "weightcapacity": "",
                        "foundationoptions": [],
                        "mattressprotectors": [],
                        "childskus": [],
                        "boughttogether": [],
                        "webcollections": [{
                            "name": "Furniture",
                            "vendor": "",
                            "category": ""
                        },
                        {
                            "name": "Misc.",
                            "vendor": "",
                            "category": ""
                        }]
                    }
                }

                ]
        }';

        $itemsDetail    = json_decode($jsonData,true);
        $errors         = $itemsDetail['errors'];
        $items          = $itemsDetail['items'];
  


        if (isset($items)) {
            try {
                $sourceImport = $this->getSourceImport();
                $data = ['status' => 'Success'];
                $errorLog = $sourceImport->importApiSource($items);
                if ($errorLog) {
                    $errorArray = explode("\n", $errorLog);
                    foreach ($errorArray as $line) {
                        if (!empty($line)) {
                            $this->messageManager->addError($line);
                        }
                    }
                }

            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $data = ['status' => 'Error','message'=> $e->getMessage()];
            }

            
            $result = $this->jsonResultFactory->create();
            $result->setData($data);
            return $result;

        }
    

        $data = ['status' => 'Error','message'=> 'Sorry, but the data is invalid '];
        $result = $this->jsonResultFactory->create();
        $result->setData($data);
        return $result;


    }
   
    public function executeold()
    {
         
        $response = $this->_apiCallHelper->inetProductSyncUp();

        $itemDetails = json_decode($response,true);
       
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/syncapi.log');
$logger = new \Zend\Log\Logger();
$logger->addWriter($writer);
//$sku = $jsonArray['items'][0]['sku'];
$logger->info(print_r($response, true));
$logger->info(print_r($itemDetails, true));
        exit;


        
    
        

        
    }

    public function processingImport($source)
    {
        // @todo: do we need this method?
    }

    private function getSourceImport()
    {
        if (!$this->sourceImport) {
            $this->sourceImport = $this->_objectManager->get('Mancini\ProductConsole\Model\ImportApiSource');
        }
        return $this->sourceImport;
    }

    /**
     * @return Import
     * @deprecated
     */
    private function getImport()
    {
        if (!$this->import) {
            $this->import = $this->_objectManager->get(Import::class);
        }
        return $this->import;
    }
}
 