<?php
namespace Synchrony\DigitalBuy\Setup;

use Magento\Cms\Model\PageRepository;
use Magento\Cms\Model\PageFactory;
use Magento\Cms\Model\BlockRepository;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var PageRepository;
     */
    private $pageRepository;

    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var BlockRepository
     */
    private $blockRepository;

    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var File
     */
    private $fileManager;

    /**
     * @var string
     */
    private $sampleAssetsBasePathInModule = 'view/frontend/web/images/wysiwyg/synchrony/sample_assets';

    /**
     * UpgradeData constructor.
     *
     * @param PageRepository $pageRepository
     * @param PageFactory $pageFactory
     * @param BlockRepository $blockRepository
     * @param BlockFactory $blockFactory
     * @param File $filesystem
     */
    public function __construct(
        PageRepository $pageRepository,
        PageFactory $pageFactory,
        BlockRepository $blockRepository,
        BlockFactory $blockFactory,
        Filesystem $filesystem,
        File $fileManager
    ) {
        $this->pageRepository = $pageRepository;
        $this->pageFactory = $pageFactory;
        $this->blockRepository = $blockRepository;
        $this->blockFactory = $blockFactory;
        $this->filesystem = $filesystem;
        $this->fileManager = $fileManager;
    }

    /**
     * Upgrade data for Synchrony DigitalBuy
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws \Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.1.0', '<')) {
            $this->createFinancingCmsPageStaticBlocks();
            $this->createSynchronyFinancingCmsPage();
            $this->createSynchronyPromotionBanners();
            $this->copySampleAssets();
        }
    }

    /**
     * Create Synchrony Financing CMS page
     *
     * @return void
     * @throws CouldNotSaveException
     */
    private function createSynchronyFinancingCmsPage()
    {
        $pageContent = <<<EOD
{{widget type="Magento\Cms\Block\Widget\Block" template="widget/static_block/default.phtml" block_id="synchrony_financing_page_header_apply"}}
{{widget type="Magento\Cms\Block\Widget\Block" template="widget/static_block/default.phtml" block_id="synchrony_financing_page_apply_steps"}}
{{widget type="Magento\Cms\Block\Widget\Block" template="widget/static_block/default.phtml" block_id="synchrony_financing_page_access_account"}}
{{widget type="Magento\Cms\Block\Widget\Block" template="widget/static_block/default.phtml" block_id="synchrony_financing_page_available_promos"}}
{{widget type="Magento\Cms\Block\Widget\Block" template="widget/static_block/default.phtml" block_id="synchrony_financing_page_faq"}}
{{widget type="Magento\Cms\Block\Widget\Block" template="widget/static_block/default.phtml" block_id="synchrony_financing_page_footnotes"}}
EOD;
        $identifiers = [
            'financing',
            'synchrony-financing'
        ];
        foreach ($identifiers as $identifier) {
            $page = $this->pageFactory->create()
                ->setData([
                    'title' => 'Financing',
                    'page_layout' => '1column',
                    'meta_keywords' => '',
                    'meta_description' => '',
                    'identifier' => $identifier,
                    'content_heading' => '',
                    'content' => $pageContent,
                    'is_active' => 0,
                    'stores' => [0],
                    'sort_order' => 0
                ]);
            try {
                $this->pageRepository->save($page);
                break;
            } catch (CouldNotSaveException $e) {
                continue;
            }
        }
    }

    /**
     * Create Cms Static Blocks
     *
     * @return void
     * @throws CouldNotSaveException
     */
    private function createFinancingCmsPageStaticBlocks()
    {
        $commonCss = <<<EOD
    /** common css **/
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,700;1,400&display=swap');
    .synchrony-financing {
        font-family: "Montserrat";
        color: #434343;
        letter-spacing: 0;
    }

    .widget.block.block-static-block {
        margin-bottom: 0px;
    }

    .synchrony-financing p, .synchrony-financing ul {
        font-size: 16px;
        line-height: 24px;
    }

    @media (min-width:768px)  {
        .synchrony-financing .row {
            max-width: 940px;
            margin: 0 auto;
        }
    }
EOD;

        $block1content = <<<EOD
<style xml="space">
$commonCss

     /** Section 1 **/
    .synchrony-financing.section1 {
        background-color: #E9E9E9;
        text-align: center;
    }

    .synchrony-financing.section1 .row {
        padding: 45px 30px;
    }

    .synchrony-financing.section1 h1 {
        font-weight: bold;
        font-size: 30px;
        line-height: 37px;
        margin-bottom: 20px;
    }

    .synchrony-financing.section1 h1:after {
        content: "*";
        font-size: 15px;
        line-height: 20px;
        vertical-align: top;
        margin-left: 2px;
    }

    .synchrony-financing.section1 .card-image {
        margin-bottom: 25px;
    }

    .synchrony-financing.section1 .card-image img {
        width: 165px;
    }

    .synchrony-financing.section1 p {
        font-size: 15px;
        line-height: 19px;
        margin-bottom: 20px;
    }

    .synchrony-financing.section1 .apply-now-button {
        display: inline-block;
        background-color: #006282;
        font-size: 18px;
        line-height: 22px;
        padding: 14px 25px;
        color: #FFFFFF;
        font-weight: bold;
    }

    .synchrony-financing.section1 a.apply-now-button {
        text-decoration: none;
    }

    @media (min-width:768px)  {
        .synchrony-financing.section1 h1 {
            font-size: 50px;
            line-height: 61px;
        }

        .synchrony-financing.section1 h1:after {
            font-size: 30px;
            line-height: 37px;
        }

        .synchrony-financing.section1 p {
            font-size: 20px;
            line-height: 24px;
            margin-bottom: 31px;
        }

        .synchrony-financing.section1 .card-image {
            float: left;
            width: 42%;
            text-align: right;
            padding-top: 15px;
        }

        .synchrony-financing.section1 .promo-content {
            margin-left: 42%;
            width: 58%;
            text-align: left;
        }        

        .synchrony-financing.section1 .card-image img {
            width: 308px;
            margin-right: 100px;
        }
    }
</style>
<div class="synchrony-financing section1">
    <div class="row">
        <div class="card-image column">
            <img src="{{media url="wysiwyg/synchrony/sample_assets/big_card.png"}}" width="308" height="201" alt="" />
        </div>
        <div class="promo-content column">
            <h1>Promotional<br /> Financing Available</h1>
            <p>on select items with your {{config path="payment/synchrony_digitalbuy/title"}}.</p>
            <a title="Apply Now" target="_blank" class="apply-now-button" href="{{config path="payment/synchrony_digitalbuy/apply_now_url"}}">Apply Now</a>
        </div>
    </div>
</div>
EOD;

        $block2content = <<<EOD
<style xml="space">
$commonCss

    /** Section 2 **/
    .synchrony-financing.section2 {
        padding: 30px 15px;
        text-align: center;
    }

    .synchrony-financing.section2 h2 {
        font-size: 25px;
        line-height: 30px;
        margin: 0;
    }

    .synchrony-financing.section2 h3 {
        margin: 40px 0 10px;
        text-transform: uppercase;
        font-size: 20px;
        line-height: 24px;
    }

    .synchrony-financing.section2 .row.steps .column:after {
        content: "";
        display: block;
        margin: 0 auto;
        padding-top: 25px;
        border-bottom: 1px solid #979797;
    }
    .synchrony-financing.section2 .row.steps .column:last-child:after {
        content: none;
        border-bottom: none;
    }

    .synchrony-financing.section2 .apply-now-button {
        display: inline-block;
        background-color: #006282;
        font-size: 18px;
        line-height: 22px;
        padding: 14px 25px;
        margin: 30px 0 10px 0;
        color: #FFFFFF;
        font-weight: bold;
    }

    .synchrony-financing.section2 a.apply-now-button {
        text-decoration: none;
    }

    @media (min-width:768px)  {
        .synchrony-financing.section2 {
            padding: 70px 15px;
        }

        .synchrony-financing.section2 h2 {
            font-size: 40px;
            line-height: 49px;
            margin-bottom: 70px;
        }

        .synchrony-financing.section2 h3 {
            margin-top: 20px;
        }

        .synchrony-financing.section2 .row.steps {
            max-width: 940px;
            margin: 0 auto;
        }

        .synchrony-financing.section2 .row.steps .column {
            width: 33%;
            border-right: 1px solid #979797;
        }

        .synchrony-financing.section2 .row.steps {
            display: flex;
        }

        .synchrony-financing.section2 .row.steps p {
            padding: 0 20px;
        }

        .synchrony-financing.section2 .row.steps .column:last-child {
            border-right: none;

        }

        .synchrony-financing.section2 .row.steps .column:after {
            content: none;
            border-bottom: none;
        }
    }
</style>
<div class="synchrony-financing section2">
    <div class="row">
        <h2>Applying online is quick, easy and safe!</h2>
    </div>
    <div class="row steps">
        <div class="column">
            <h3>1. Apply</h3>
            <p>Fill out a secure online application. No need to have any items selected for purchase before applying.</p>
        </div>
        <div class="column">
            <h3>2. Credit decision</h3>
            <p>You’ll get an instant credit decision after you submit the application.</p>
        </div>
        <div class="column">
            <h3>3. Shop</h3>
            <p>If you’re approved, we’ll send you a temporary account number and you can use it right away to pay for your purchase.</p>
        </div>
    </div>
    <div class="row">
        <a title="Apply Now" target="_blank" class="apply-now-button" href="{{config path="payment/synchrony_digitalbuy/apply_now_url"}}">Apply Now</a>
    </div>
</div>
EOD;

        $block3content = <<<EOD
<style xml="space">
$commonCss

    /** Section 3 **/
    .synchrony-financing.section3 {
        color: #FFFFFF;
        background-color: #006282;
        padding: 30px 30px;
        text-align: center;
    }

    .synchrony-financing.section3 h2 {
        font-weight: bold;
        font-size: 20px;
        line-height: 24px;
        margin: 0 0 20px 0;
    }

    .synchrony-financing.section3 p {
        margin-bottom: 20px;
    }

    .synchrony-financing.section3 a {
        font-weight: bold;
        text-decoration: underline;
        color: #FFFFFF;
    }

    .synchrony-financing.section3 .card-image img {
        width: 160px;
    }

    @media (min-width:768px)  {
        .synchrony-financing.section3 {
            padding: 20px 30px;
        }

        .synchrony-financing.section3 .row {
            max-width: 770px;
            margin: 0 auto;
        }

        .synchrony-financing.section3 .access-account.column {
            text-align: left;
            float: left;
            width: 70%;
        }

        .synchrony-financing.section3 h2 {
            margin: 12px 0;
        }

        .synchrony-financing.section3 p {
            margin-bottom: 9px;
        }

        .synchrony-financing.section3 .card-image.column {
            float: left;
            width: 30%;
            text-align: right;
        }

        .synchrony-financing.section3 .row:after {
            content: "";
            clear: both;
            display: table;
        }
    }
</style>
<div class="synchrony-financing section3">
    <div class="row">
        <div class="access-account column">
            <h2>Already have a {{config path="payment/synchrony_digitalbuy/title"}}?</h2>
            <p>Activate your card, make payments or manage your account.</p>
            <p><a target="_blank" title="Access Your Account" class="access-account-link" href="https://www.mysynchrony.com/">Access Your Account</a></p>
        </div>
        <div class="card-image column">
            <img src="{{media url="wysiwyg/synchrony/sample_assets/small_card.png"}}" width="165" height="107" alt="" />
        </div>
    </div>
</div>
EOD;

        $block4content = <<<EOD
<style xml="space">
$commonCss
    
    /** Section 4 **/
    .synchrony-financing.section4 {
        background-color: #E9E9E9;
        padding: 30px 30px 40px;
        text-align: center;
    }

    .section4{
        background-color: #E9E9E9;
        padding: 0 20px;
    }

    .synchrony-financing.section4 h2 {
        font-weight: bold;
        font-size: 25px;
        line-height: 30px;
        margin: 0;
    }

    .synchrony-financing.section4 h3 {
        text-transform: uppercase;
        font-size: 20px;
        font-weight: bold;
        line-height: 24px;
        margin: 40px 0 20px 0;
    }

    .synchrony-financing.section4 h3:after {
        content: "*";
        font-size: 15px;
        line-height: 20px;
        vertical-align: top;
        margin-left: 2px;
        white-space: nowrap;
    }

    @media (min-width:768px)  {
        .synchrony-financing.section4 h2 {
            margin-top: 40px;
            font-weight: bold;
            font-size: 40px;
            font-weight: 49px;
        }

        .synchrony-financing.section4 h3 {
            margin: 50px 7px 20px;
        }

        .synchrony-financing.section4 .available-promotions.column {
            float: left;
            width: 33%;
        }
        .synchrony-financing.section4 .available-promotions.column p {
            padding: 0 10px;
        }

        .synchrony-financing.section4 .row:after {
            content: "";
            clear: both;
            display: table;
        }
    }
</style>
<div class="synchrony-financing section4">
    <div class="row">
        <h2>Available Promotions</h2>
    </div>
    <div class="row">
        <div class="available-promotions column">
            <h3>XX Months Promotional Financing</h3>
            <p>On  &lt;insert product limitations&gt; purchases &lt;of \$XXX or more&gt; &lt;after discounts> made with your {{config path="payment/synchrony_digitalbuy/title"}}  &lt;between Date – Date&gt;.</p>
        </div>
        <div class="available-promotions column">
            <h3>XX Months Promotional Financing</h3>
            <p>On  &lt;insert product limitations&gt; purchases &lt;of \$XXX or more&gt; &lt;after discounts> made with your {{config path="payment/synchrony_digitalbuy/title"}}  &lt;between Date – Date&gt;.</p>
        </div>
        <div class="available-promotions column">
            <h3>XX Months Promotional Financing</h3>
            <p>On  &lt;insert product limitations&gt; purchases &lt;of \$XXX or more&gt; &lt;after discounts> made with your {{config path="payment/synchrony_digitalbuy/title"}}  &lt;between Date – Date&gt;.</p>
        </div>
    </div>
</div>
EOD;
        $block5content = <<<EOD
<style xml="space">
    $commonCss

    /** Section 5 **/
    .synchrony-financing.section5 {
        padding: 50px 15px;
    }

    .synchrony-financing.section5 h2 {
        font-size: 25px;
        line-height: 30px;
        margin: 0;
    }

    .synchrony-financing.section5 .row h3 {
        font-size: 20px;
        line-height: 24px;
        font-weight: bold;
        margin: 40px 0 0px;
    }

    .synchrony-financing.section5 .row p {
        padding-top: 20px;
    }

    .synchrony-financing.section5 .row ul {
        margin: 0;
    }

    .synchrony-financing.section5 .row ul > li {
        margin: 10px;
    }

    .synchrony-financing.section5 .row.syf-account p {
        padding-bottom: 20px;
    }

    .synchrony-financing.section5 a {
        font-weight: bold;
        text-decoration: underline;
        color: #006282;
    }

    @media (min-width:768px)  {
        .synchrony-financing.section5 h1 {
            font-size: 40px;
            line-height: 49px;
        }

        .synchrony-financing.section5 .column {
            float: left;
        }

        .synchrony-financing.section5 .syf-account .column {
            width: 30%;
        }

        .synchrony-financing.section5 .syf-account .column:last-child {
            width: 70%;
        }

        .synchrony-financing.section5 .pay-bill .column {
            width: 50%;
        }

        .synchrony-financing.section5 .pay-bill .column p {
            padding: 20px 20px 0 0;
        }

        .synchrony-financing.section5 .row:after {
            content: "";
            clear: both;
            display: table;
        }
    }
</style>
<div class="synchrony-financing section5">
    <div class="row">
        <h2>Frequently Asked Questions</h2>
    </div>
    <div class="syf-apply row">
        <h3>How do I apply for a {{config path="payment/synchrony_digitalbuy/title"}}?</h3>
        <p>You can apply for a {{config path="payment/synchrony_digitalbuy/title"}} by filling out an application online. <a target="_blank" title="Apply Now" href="{{config path="payment/synchrony_digitalbuy/apply_now_url"}}">Apply Now</a></p>
    </div>
    <div class="syf-account row">
        <h3>Where do I log into my {{config path="payment/synchrony_digitalbuy/title"}} account?</h3>
        <p>To log into your {{config path="payment/synchrony_digitalbuy/title"}} account, <a title="Click here" target="_blank" href="https://www.mysynchrony.com/">click here</a> to go to the Synchrony® Bank website. After
            logging into your account, you will be able to:</p>
        <div class="column">
            <ul>
                <li>Make a payment</li>
                <li>View your balance</li>
            </ul>
        </div>
        <div class="column">
            <ul>
                <li>Update your account information</li>
                <li>Schedule up to 12 future payments</li>
            </ul>
        </div>
    </div>
    <div class="pay-bill row">
        <h3>How can I make a payment on my {{config path="payment/synchrony_digitalbuy/title"}} account?</h3>
        <p>There are three convenient ways to pay your bill.</p>
        <div class="column">
            <p><strong>Pay Online:</strong> <a title="Click here" target="_blank" href="https://www.mysynchrony.com/">Click here to manage your account</a> and make payments online.</p>
            <p><strong>Pay by Phone:</strong> Contact Synchrony Bank's main
                customer service line at 877-295-2080 during the hours
                listed below to make a payment by phone.</p>
            <p style="font-style: italic;">Mon-Fri: 8:00 AM to 12:00 AM ET
                There is a fee for payments made via phone.</p>
        </div>
        <div class="column">
            <p><strong>Pay by Mail:</strong> Mail payments to the address below.</p>
            <p>Ensure that you have plenty of time for your payment to
                arrive prior to the due date.</p>
            <p>
                Synchrony Bank<br/>
                PO Box 960061<br/>
                Orlando, FL 32896-0061<br/>
            </p>
        </div>
    </div>
</div>
EOD;
        $block6content = <<<EOD
<style xml="space">
$commonCss

     /** Section 6 **/
    .synchrony-financing.section6 {
        padding: 50px 15px;
    }

    .synchrony-financing.section6 p {
        font-size: 14px;
        line-height: 24px;
    }
    .synchrony-financing.section6:before {
        content: "";
        display: block;
        margin: 0 auto;
        width: 100%;
        padding-top: 20px;
        border-top: 1px solid #979797;
    }
</style>
<div class="synchrony-financing section6">
    <div class="row">
        <p>* Subject to Credit Approval. Minimum monthly payments required. We reserve the right to discontinue or alter the terms of this offer at any time. Credit is extended by Synchrony Bank.<br />Additional promotional financing options may be available. Ask us for details.</p>
    </div>
</div>
EOD;

        $blocks = [
            [
                'title' => 'Synchrony Financing Page Header Apply',
                'identifier' => 'synchrony_financing_page_header_apply',
                'content' => $block1content,
                'stores' => [0],
                'is_active' => 1,
            ],
            [
                'title' => 'Synchrony Financing Page Apply Steps',
                'identifier' => 'synchrony_financing_page_apply_steps',
                'content' => $block2content,
                'stores' => [0],
                'is_active' => 1,
            ],
            [
                'title' => 'Synchrony Financing Page Access Account',
                'identifier' => 'synchrony_financing_page_access_account',
                'content' => $block3content,
                'stores' => [0],
                'is_active' => 1,
            ],
            [
                'title' => 'Synchrony Financing Page Available Promotions',
                'identifier' => 'synchrony_financing_page_available_promos',
                'content' => $block4content,
                'stores' => [0],
                'is_active' => 1,
            ],
            [
                'title' => 'Synchrony Financing Page FAQ',
                'identifier' => 'synchrony_financing_page_faq',
                'content' => $block5content,
                'stores' => [0],
                'is_active' => 1,
            ],
            [
                'title' => 'Synchrony Financing Page Footnotes',
                'identifier' => 'synchrony_financing_page_footnotes',
                'content' => $block6content,
                'stores' => [0],
                'is_active' => 1,
            ]
        ];

        foreach ($blocks as $block) {
            try {
                $this->blockRepository->save($this->blockFactory->create()->setData($block));
            } catch (CouldNotSaveException $e) {
                continue;
            }
        }
    }

    /**
     * Create Synchrony Financing CMS page
     *
     * @return void
     * @throws CouldNotSaveException
     */
    private function createSynchronyPromotionBanners()
    {
        $blocks = [
            [
                'title' => 'Synchrony Generic Banner (160x600)',
                'identifier' => 'synchrony-promo-generic-banner-160-600',
                'content' => '<a href="{{config path="web/secure/base_url"}}financing"><img src="{{media url="wysiwyg/synchrony/sample_assets/SYF_DPS-0035_-_Banners_-_Generic_-_160x600.png"}}" width="160" height="600" alt="Synchrony Credit Card Banner" /></a>',
                'stores' => [0],
                'is_active' => 0,
            ],
            [
                'title' => 'Synchrony Generic Banner (300x200)',
                'identifier' => 'synchrony-promo-generic-banner-300-200',
                'content' => '<a href="{{config path="web/secure/base_url"}}financing"><img src="{{media url="wysiwyg/synchrony/sample_assets/SYF_DPS-0035_-_Banners_-_Generic_-_300x200.png"}}" width="300" height="200" alt="Synchrony Credit Card Banner" /></a>',
                'stores' => [0],
                'is_active' => 0,
            ],
            [
                'title' => 'Synchrony Generic Banner (720x90)',
                'identifier' => 'synchrony-promo-generic-banner-720-90',
                'content' => '<a href="{{config path="web/secure/base_url"}}financing"><img src="{{media url="wysiwyg/synchrony/sample_assets/SYF_DPS-0035_-_Banners_-_Generic_-_720x90.png"}}" width="720" height="90" alt="Synchrony Credit Card Banner" /></a>',
                'stores' => [0],
                'is_active' => 0,
            ],
            [
                'title' => 'Synchrony HOME Banner (160x600)',
                'identifier' => 'synchrony-promo-home-banner-160-600',
                'content' => '<a href="{{config path="web/secure/base_url"}}financing"><img src="{{media url="wysiwyg/synchrony/sample_assets/SYF_DPS-0035_-_Banners_-_Home_Promotional_Financing_-_160x600.jpg"}}" width="160" height="600" alt="HOME Credit Card Banner" /></a>',
                'stores' => [0],
                'is_active' => 0,
            ],
            [
                'title' => 'Synchrony HOME Banner (300x200)',
                'identifier' => 'synchrony-promo-home-banner-300-200',
                'content' => '<a href="{{config path="web/secure/base_url"}}financing"><img src="{{media url="wysiwyg/synchrony/sample_assets/SYF_DPS-0035_-_Banners_-_Home_Promotional_Financing_-_300x200.png"}}" width="300" height="200" alt="HOME Credit Card Banner" /></a>',
                'stores' => [0],
                'is_active' => 0,
            ],
            [
                'title' => 'Synchrony HOME Banner (720x90)',
                'identifier' => 'synchrony-promo-home-banner-720-90',
                'content' => '<a href="{{config path="web/secure/base_url"}}financing"><img src="{{media url="wysiwyg/synchrony/sample_assets/SYF_DPS-0035_-_Banners_-_Home_Promotional_Financing_-_720x90.png"}}" width="720" height="90" alt="HOME Credit Card Banner" /></a>',
                'stores' => [0],
                'is_active' => 0,
            ],
            [
                'title' => 'Synchrony Car Care Banner (160x600)',
                'identifier' => 'synchrony-promo-car-care-banner-160-600',
                'content' => '<a href="{{config path="web/secure/base_url"}}financing"><img src="{{media url="wysiwyg/synchrony/sample_assets/SYF_DPS-0035_-_Car_Care_Banner_Set_-_160x600.jpg"}}" width="160" height="600" alt="Car Care Credit Card Banner" /></a>',
                'stores' => [0],
                'is_active' => 0,
            ],
            [
                'title' => 'Synchrony Car Care Banner (300x200)',
                'identifier' => 'synchrony-promo-car-care-banner-300-200',
                'content' => '<a href="{{config path="web/secure/base_url"}}financing"><img src="{{media url="wysiwyg/synchrony/sample_assets/SYF_DPS-0035_-_Car_Care_Banner_Set_-_300x200.jpg"}}" width="300" height="200" alt="Car Care Credit Card Banner" /></a>',
                'stores' => [0],
                'is_active' => 0,
            ],
            [
                'title' => 'Synchrony Car Care Banner (720x90)',
                'identifier' => 'synchrony-promo-car-care-banner-720-90',
                'content' => '<a href="{{config path="web/secure/base_url"}}financing"><img src="{{media url="wysiwyg/synchrony/sample_assets/SYF_DPS-0035_-_Car_Care_Banner_Set_-_720x90.png"}}" width="720" height="90" alt="Car Care Credit Card Banner" /></a>',
                'stores' => [0],
                'is_active' => 0,
            ],
            [
                'title' => 'Synchrony Text Banner',
                'identifier' => 'synchrony-promo-text-banner',
                'content' => '<div style="margin: 0; padding: .5em; background-color: #006282; color: white;">PROMOTIONAL FINANCING AVAILABLE. <a style="font-weight: bold; text-decoration: underline; color: #ffffff;" href="{{config path="web/secure/base_url"}}financing">LEARN MORE.</a></div>',
                'stores' => [0],
                'is_active' => 0,
            ],
            [
                'title' => 'Synchrony Value Prop Text Block',
                'identifier' => 'synchrony-value-prop-text',
                'content' => '<p style="font-weight: bold; margin-bottom: 0; font-size: larger;">Apply for a {{config path="payment/synchrony_digitalbuy/title"}}</p><p style="margin-bottom: 20px;">We have financing options available with convenient monthly payments. <a style="text-decoration: underline;" title="Apply Now" target="_blank" href="{{config path="payment/synchrony_digitalbuy/apply_now_url"}}">Apply Now.</a></p>',
                'stores' => [0],
                'is_active' => 1,
            ]
        ];

        foreach ($blocks as $block) {
            try {
                $this->blockRepository->save($this->blockFactory->create()->setData($block));
            } catch (CouldNotSaveException $e) {
                continue;
            }
        }
    }

    /**
     * Copy sample assets to media folder
     */
    private function copySampleAssets()
    {
        $sourceDir = dirname(dirname(__FILE__)) . '/' . $this->sampleAssetsBasePathInModule;

        try {
            $mediaDirectory = $this->filesystem->getDirectoryWrite(
                \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
            );
            $destinationDir = $mediaDirectory->getAbsolutePath() . 'wysiwyg/synchrony/sample_assets';
            // create destination dir if doesn't exist
            $this->fileManager->checkAndCreateFolder($destinationDir);
            $this->fileManager->cd($sourceDir);
            $files = $this->fileManager->ls(File::GREP_FILES);
            foreach ($files as $file) {
                if (!$file['is_image']) {
                    continue;
                }
                $this->fileManager->cp($sourceDir . '/' . $file['text'], $destinationDir . '/' . $file['text']);
            }
        } catch (\Exception $e) {
            // intentionally doing nothing, any issues here do not worth interrupting installation
        }
    }
}
