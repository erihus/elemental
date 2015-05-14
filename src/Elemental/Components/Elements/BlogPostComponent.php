<?php namespace Elemental\Components\Elements;


class BlogPostComponent extends BaseElement {

    public function __construct() {

        $this->fields = [
            'header_image' => 'image',
            'alt_text' => 'text',
            'date' => 'date',
            'title' => 'text',
            'author' => 'text',
            'url_slug' => 'text',
            'excerpt' => 'wysiwyg',
            'body' => 'wysiwyg',
            'categories' => 'multiselect',
            'tags' => 'multiselect'
        ];

         $this->labels = [
            'header_image' => 'Header Image (Recommended File Type: .jpg or .png)',
            'alt_text' => 'Header Image Alt Text',
            'date' => 'Post Date',
            'title' => 'Post Title',
            'author' => 'Author',
            'url_slug' => 'URL Slug',
            'excerpt' => 'Excerpt',
            'body' => 'Body Text',
            'categories' => 'Categories',
            'tags' => 'Tags'
        ];

        $this->rules = [
            'title' => 'required',
            'date' => 'required'
        ];

        $this->options = [
            'categories' => 'api/element/type/blog-category',
            'tags' => 'api/collection/type/tags/children'
        ];

    }

}