<?php

namespace Packback\Lti1p3;

class LtiDeepLinkResource
{
    private $type = 'ltiResourceLink';
    private $title;
    private $text;
    private $url;
    private $lineitem;
    private $icon;
    private $thumbnail;
    private $custom_params = [];
    private $target = 'iframe';

    /**
     * @return LtiDeepLinkResource
     */
    public static function new()
    {
        return new LtiDeepLinkResource();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $value string
     *
     * @return $this
     */
    public function setType($value)
    {
        $this->type = $value;

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
     * @param $value string
     *
     * @return $this
     */
    public function setTitle($value)
    {
        $this->title = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param $value string
     *
     * @return $this
     */
    public function setText($value)
    {
        $this->text = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param $value string
     *
     * @return $this
     */
    public function setUrl($value)
    {
        $this->url = $value;

        return $this;
    }

    /**
     * @return LtiLineitem
     */
    public function getLineitem()
    {
        return $this->lineitem;
    }

    /**
     * @param $value LtiLineitem
     *
     * @return $this
     */
    public function setLineitem(LtiLineitem $value)
    {
        $this->lineitem = $value;

        return $this;
    }

    /**
     * @param $icon LtiDeepLinkResourceIcon
     *
     * @return $this
     */
    public function setIcon(LtiDeepLinkResourceIcon $icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return LtiDeepLinkResourceIcon
     */
    public function getIcon(): LtiDeepLinkResourceIcon
    {
        return $this->icon;
    }

    /**
     * @param $thumbnail LtiDeepLinkResourceIcon
     *
     * @return $this
     */
    public function setThumbnail(LtiDeepLinkResourceIcon $thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * @return LtiDeepLinkResourceIcon
     */
    public function getThumbnail(): LtiDeepLinkResourceIcon
    {
        return $this->thumbnail;
    }

    /**
     * @return array
     */
    public function getCustomParams()
    {
        return $this->custom_params;
    }

    /**
     * @param $value array
     *
     * @return $this
     */
    public function setCustomParams($value)
    {
        $this->custom_params = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param $value string
     *
     * @return $this
     */
    public function setTarget($value)
    {
        $this->target = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $resource = [
            'type' => $this->type,
            'title' => $this->title,
            'text' => $this->text,
            'url' => $this->url,
            'presentation' => [
                'documentTarget' => $this->target,
            ],
        ];
        if (!empty($this->custom_params)) {
            $resource['custom'] = $this->custom_params;
        }
        if (isset($this->icon)) {
            $resource['icon'] = $this->icon->toArray();
        }
        if (isset($this->thumbnail)) {
            $resource['thumbnail'] = $this->thumbnail->toArray();
        }
        if ($this->lineitem !== null) {
            $resource['lineItem'] = [
                'scoreMaximum' => $this->lineitem->getScoreMaximum(),
                'label' => $this->lineitem->getLabel(),
            ];
        }

        return $resource;
    }
}
