<?php

namespace NSWDPC\Elemental\Models\Blog;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Blog\Model\Blog;
use SilverStripe\Blog\Model\BlogTag;
use SilverStripe\Blog\Model\BlogPost;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataList;

/**
 * ElementBlog
 * Adds an element listing matching blogpost records
 * @property ?string $HTML
 * @property int $NumberOfPosts
 * @property ?string $BlogLinkTitle
 * @property int $BlogID
 * @property int $TagID
 * @method \SilverStripe\Blog\Model\Blog Blog()
 * @method \SilverStripe\Blog\Model\BlogTag Tag()
 * @mixin \NSWDPC\GridHelper\Extensions\ElementChildGridExtension
 */
class ElementBlog extends BaseElement
{
    /**
     * @inheritdoc
     */
    private static string $icon = 'font-icon-thumbnails';

    /**
     * @inheritdoc
     */
    private static string $table_name = 'ElementBlog';

    /**
     * @inheritdoc
     */
    private static string $title = 'Blog list';

    /**
     * @inheritdoc
     */
    private static string $description = "Display a list of Blog items";

    /**
     * @inheritdoc
     */
    private static string $singular_name = 'Blog';

    /**
     * @inheritdoc
     */
    private static string $plural_name = 'Blogs';

    /**
     * @inheritdoc
     */
    private static array $db = [
        'HTML' => 'HTMLText',
        'NumberOfPosts' => 'Int',
        'BlogLinkTitle' => 'Varchar(255)'
    ];

    /**
     * @inheritdoc
     */
    private static array $defaults = [
        'NumberOfPosts' => 4
    ];

    /**
     * @inheritdoc
     */
    private static array $has_one = [
        'Blog' => Blog::class,
        'Tag' => BlogTag::class
    ];

    /**
     * @inheritdoc
     */
    #[\Override]
    public function getType()
    {
        return _t(self::class . '.BlockType', 'Blog list');
    }

    /**
     * @inheritdoc
     */
    #[\Override]
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(
            function ($fields): void {

                /** @var \SilverStripe\Forms\HTMLEditor\HTMLEditorField $editorField */
                $editorField = $fields->fieldByName('Root.Main.HTML');
                $editorField->setTitle(_t(self::class . '.ContentLabel', 'Content'));

                $fields->removeByName(['BlogID','TagID']);
                $tags = BlogTag::get()->map('ID', 'Title');
                $fields->addFieldsToTab(
                    'Root.Main',
                    [
                        DropdownField::create(
                            'BlogID',
                            _t(
                                self::class . '.HOLDER_ID',
                                'Choose a blog'
                            ),
                            $this->getBlogs()
                        )->setEmptyString('Choose an option'),
                        TextField::create(
                            'BlogLinkTitle',
                            _t(
                                self::class . '.LINKTITLE',
                                'Title for link to view the blog selected'
                            )
                        ),
                        DropdownField::create(
                            'TagID',
                            'Tag',
                            $tags ?? []
                        )->setEmptyString(
                            _t(
                                self::class . '.CHOOSE_AN_OPTION',
                                'Choose an option'
                            )
                        ),
                        NumericField::create(
                            'NumberOfPosts',
                            _t(
                                self::class . '.POSTS',
                                'Number of Posts'
                            )
                        )->setDescription(
                            _t(
                                self::class . '.POSTS_DESCRIPTION',
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
    #[\Override]
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $this->NumberOfPosts = abs($this->NumberOfPosts);
    }

    /**
     * Return all Blog objects
     */
    public function getBlogs(): DataList
    {
        return Blog::get();
    }

    /**
     * Get all recent posts based on filters and limit
     */
    public function getRecentPosts(): ?DataList
    {
        $blog = $this->Blog();
        if (!$blog || !$blog->exists()) {
            return null;
        }

        $blogPosts = BlogPost::get()
            ->sort('PublishDate', 'DESC')
            ->filter([
                'ParentID' => $blog->ID
            ]);
        $tag = $this->Tag();
        if ($tag && $tag->exists() && $tag->Title) {
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
