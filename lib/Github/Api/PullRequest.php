<?php

/**
 * API for accessing Pull Requests from your Git/Github repositories.
 *
 * @link      http://develop.github.com/p/pulls.html
 * @author    Nicolas Pastorino <nicolas.pastorino at gmail dot com>
 * @license   MIT License
 */
class Github_Api_PullRequest extends Github_Api
{
    /**
     * Get a listing of a project's pull requests by the username, repo, and optionnally state.
     *
     * @link    http://developer.github.com/v3/pulls/
     * @param   string $username          the username
     * @param   string $repo              the repo
     * @param   string $state             the state of the fetched pull requests.
     *                                    The API seems to automatically default to 'open'
     * @return  array                     array of pull requests for the project
     */
    public function listPullRequests($username, $repo, $state = 'open')
    {
        $params = array();
        if(!empty($state)) {
            $params['state'] = $state;
        }
        $response = $this->get('repos/'.urlencode($username).'/'.urlencode($repo).'/pulls', $params);
        return $response;
    }

    /**
     * Show all details of a pull request, including the discussions.
     *
     * @link      http://develop.github.com/p/pulls.html
     * @param   string $username          the username
     * @param   string $repo              the repo
     * @param   string $pullRequestId     the ID of the pull request for which details are retrieved
     * @return  array                     array of pull requests for the project
     */
    public function show($username, $repo, $pullRequestId)
    {
        $response = $this->get('repos/'.urlencode($username).'/'.urlencode($repo).'/pulls/'.urlencode($pullRequestId));
        return $response;
    }

    /**
     * Create a pull request
     *
     * @link      http://develop.github.com/p/pulls.html
     * @param   string $username          the username
     * @param   string $repo              the repo
     * @param   string $base              A String of the branch or commit SHA that you want your changes to be pulled to.
     * @param   string $head              A String of the branch or commit SHA of your changes.
     *                                    Typically this will be a branch. If the branch is in a fork of the original repository,
     *                                    specify the username first: "my-user:some-branch".
     * @param   string $title             The String title of the Pull Request. Used in pair with $body.
     * @param   string $body              The String body of the Pull Request. Used in pair with $title.
     * @param   int $issueId              If a pull-request is related to an issue, place issue ID here. The $title-$body pair and this are mutually exclusive.
     * @return  array                     array of pull requests for the project
     */
    public function create($username, $repo, $base, $head, $title = null, $body = null, $issueId = null)
    {
        $postParameters = array( 'pull[base]' => $base,
                                 'pull[head]' => $head
                          );

        if ( $title !== null and $body !== null ) {
            $postParameters = array_merge( $postParameters,
                                           array(
                                             'pull[title]' => $title,
                                             'pull[body]'  => $body
                                           )
                                         );
        } elseif ( $issueId !== null ) {
            $postParameters = array_merge( $postParameters,
                                           array(
                                             'pull[issue]' => $issueId
                                           )
                                         );
        } else {
            // @FIXME : Exception required here.
            return null;
        }

        $response = $this->post('repos/'.urlencode($username).'/'.urlencode($repo) . '/pulls',
                                $postParameters
                               );

        // @FIXME : Exception to be thrown when $response['error'] exists.
        //          Content of error can be : "{"error":["A pull request already exists for <username>:<branch>."]}"
        return $response;
    }

    /**
     * Is the Merge Committed?
     *
     * @param   string $username          the username
     * @param   string $repo              the repo
     * @param   string $pullRequestId     the ID of the pull request for which details are retrieved
     * @return  boolean                   true is yes and false if not
     */
    public function isMerged($username, $repo, $pullRequestId)
    {
        try {
            $this->get('repos/'.urlencode($username).'/'.urlencode($repo) . '/pulls/' . urlencode($pullRequestId) . '/merge');
        } catch (Github_HttpClient_Exception $ghce) {
            // if this is thrown then it's a 404
            return false;
        }

        return true;
    }

    /**
     * Do the merge on github
     *
     * @param   string $username          the username
     * @param   string $repo              the repo
     * @param   string $pullRequestId     the ID of the pull request for which details are retrieved
     * @param   string $message           the commit message
     * @return  array                     array of pull requests for the project
     */
    public function merge($username, $repo, $pullRequestId, $message)
    {
        $params = array('commit_message' => $message);
        $response = $this->put('repos/'.urlencode($username).'/'.urlencode($repo) . '/pulls/' . urlencode($pullRequestId) . '/merge', $params);

        return $response;
    }

    /**
     * List all the files in the pull request
     *
     * @param   string $username          the username
     * @param   string $repo              the repo
     * @param   string $pullRequestId     the ID of the pull request for which details are retrieved
     * @return  array                     array of pull requests for the project
     */
    public function listFiles($username, $repo, $pullRequestId)
    {
        $response = $this->get('repos/'.urlencode($username).'/'.urlencode($repo) . '/pulls/' . urlencode($pullRequestId) . '/files');

        return $response;
    }

    /**
     * List all the commits in the pull request
     *
     * @param   string $username          the username
     * @param   string $repo              the repo
     * @param   string $pullRequestId     the ID of the pull request for which details are retrieved
     * @return  array                     array of pull requests for the project
     */
    public function listCommits($username, $repo, $pullRequestId)
    {
        $response = $this->get('repos/'.urlencode($username).'/'.urlencode($repo) . '/pulls/' . urlencode($pullRequestId) . '/commits');

        return $response;
    }
}
