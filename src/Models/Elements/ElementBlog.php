<?php
namespace NSWDPC\Elemental\Models\Blog;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Blog\Model\Blog;
use SilverStripe\Blog\Model\BlogTag;
use SilverStripe\Blog\Model\BlogPost;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\ListboxField;
use SilverStripe\ORM\DataList;

/**
 * ElementBlog
 * Adds an element listing matching blogpost records
 */
class ElementBlog extends BaseElement {

    /**
     * @inheritdoc
     */
    private static $icon = 'font-icon-thumbnails';

    /**
     * @inheritdoc
     */
    private static $table_name = 'ElementBlog';

    /**
     * @inheritdoc
     */
    private static $title = 'Blog list';

    /**
     * @inheritdoc
     */
    private static $description = "Display a list of Blog items";

    /**
     * @inheritdoc
     */
    private static $singular_name = 'Blog';

    /**
     * @inheritdoc
     */
    private static $plural_name = 'Blogs';

    /**
     * @inheritdoc
     */
    private static $db = [
        'HTML' => 'HTMLText',
        'NumberOfPosts' => 'Int',
        'BlogLinkTitle' => 'Varchar(255)'
    ];

    /**
     * @inheritdoc
     */
    private static $defaults = [
        'NumberOfPosts' => 4
    ];

    /**
     * @inheritdoc
     */
    private static $has_one = [
        'Blog' => Blog::class,
        'Tag' => BlogTag::class
    ];

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return _t(__CLASS__ . '.BlockType', 'Blog list');
    }

    /**
     * @inheritdoc
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(
            function($fields) {

                /** @var HTMLEditorField $editorField */
                $editorField = $fields->fieldByName('Root.Main.HTML');
                $editorField->setTitle(_t(__CLASS__ . '.ContentLabel', 'Content'));

                $fields->removeByName(['BlogID','TagID']);
                $tags = BlogTag::get()->map('ID', 'Title');
                $fields->addFieldsToTab(
                    'Root.Main', [
                        DropdownField::create(
                            'BlogID',
                            _t(
                                __CLASS__ . '.HOLDER_ID',
                                'Choose a blog'
                            ),
                            $this->getBlogs()
                        )->setEmptyString('Choose an option'),
                        TextField::create(
                            'BlogLinkTitle',
                            _t(
                                __CLASS__ . '.LINKTITLE',
                                'Title for link to view the blog selected'
                            )
                        ),
                        DropdownField::create(
                            'TagID',
                            'Tag',
                            $tags ?? []
                        )->setEmptyString(
                            _t(
                                __CLASS__ . '.CHOOSE_AN_OPTION',
                                'Choose an option'
                            )
                        ),
                        NumericField::create(
                            'NumberOfPosts',
                            _t(
                                __CLASS__ . '.POSTS',
                                'Number of Posts'
                            )
                        )->setDescription(
                            _t(
                                __CLASS__ . '.POSTS_DESCRIPTION',
                                'Setting this value to zero will return all matching posts'
                            )
                        )
                    ]
                );

            }
        );
        return parent::getCMSFields();
    }

    /**
     * @inheritdoc
     */
    public function onBeforeWrite() {
        parent::onBeforeWrite();
        $this->NumberOfPosts = abs($this->NumberOfPosts);
    }

    /**
     * Return all Blog objects
     */
    public function getBlogs() : DataList {
        return Blog::get();
    }

    /**
     * Get all recent posts based on filters and limit
     */
    public function getRecentPosts() : ?DataList
    {
        $blog = $this->Blog();
        if(!$blog || !$blog->exists()) {
            return null;
        }
        $blogPosts = BlogPost::get()
            ->sort('PublishDate', 'DESC')
            ->filter([
                'ParentID' => $blog->ID
            ]);
        $tag = $this->Tag();
        if($tag && $tag->exists() && $tag->Title) {
            $blogPosts = $blogPosts->filter([
                'Tags.ID' => $tag->ID
            ]);
        }
        if ($blogPosts && $this->NumberOfPosts > 0) {
            $blogPosts = $blogPosts->limit($this->NumberOfPosts);
        }
        return $blogPosts;
    }


}
