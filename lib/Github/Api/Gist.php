<?php
/**
 * Getting information on gists
 *
 * @link      http://developer.github.com/v3/gists/
 * @author    Jon Whitcraft
 * @license   MIT License
 */
class Github_Api_Gist extends Github_Api
{
    /**
     * Fetch all public gists
     *
     * @param int $page             the page to return
     * @return array
     */
    public function getPublicGists($page = 1)
    {
        return $this->get('gists/public', array('page' => $page));
    }

    /**
     * Get gists for the authenticated user.
     *
     * @param int $page             the page to return
     * @param bool $starred         return only starred gists
     * @return array
     */
    public function getGists($page = 1, $starred = false)
    {
        $url = ($starred == true) ? "gists/starred" : "gists";
        return $this->get($url, array('page' => $page));
    }

    /**
     * Fetch a Specific Gist
     *
     * @param string $gistId        id of the gist
     * @return array
     */
    public function getGist($gistId)
    {
        return $this->get('gists/' . $gistId);
    }

    /**
     * Star a Gist for the authenticated user
     *
     * @param string $gistId        id of the gist
     * @return array
     */
    public function starGist($gistId)
    {
        return $this->put('gists/' . $gistId . '/star');
    }

    /**
     * Remove a Star from a Gist for the authenticated user
     *
     * @param string $gistId        id of the gist
     * @return array
     */
    public function unstarGist($gistId)
    {
        return $this->delete('gists/' . $gistId . '/star');
    }

    /**
     * Check if a gist is stared for the authenticated user
     *
     * @param string $gistId        id of the gist
     * @return bool
     */
    public function isGistStarred($gistId)
    {
        try {
            $this->get('gists/' . $gistId . '/star');
        } catch (Github_HttpClient_Exception $ghce) {
            return false;
        }
        return true;
    }


    /**
     * Create a new Gist for the authenticated user
     *
     * @param boolean $public           is the gist public or not
     * @param array $files              list of the files to create
     * @param string $description       description of the gist
     * @return void
     */
    public function createGist($public, array $files, $description = '')
    {
        
    }

    /**
     * Delete a Gist for the authenticated user
     * @param string $gistId        id of the gist
     * @return bool
     */
    public function deleteGist($gistId)
    {
        try {
            $this->delete('gists/' . $gistId);
        } catch (Github_HttpClient_Exception $ghce) {
            return false;
        }
        return true;
    }
}