<?php

/**
 * Getting information on specific commits,
 * the diffs they introduce, the files they've changed.
 *
 * @link      http://develop.github.com/p/commits.html
 * @author    Thibault Duplessis <thibault.duplessis at gmail dot com>
 * @license   MIT License
 */
class Github_Api_Commit extends Github_Api
{
    /**
     * List commits by username, repo and branch
     * http://develop.github.com/p/commits.html#listing_commits_on_a_branch
     *
     * @param   string  $username         the username
     * @param   string  $repo             the repo
     * @param   string  $branch           the branch
     * @throws Github_Api_Exception
     * @return  array                     list of users found
     */
    public function getBranchCommits($username, $repo, $branch)
    {
        throw new Github_Api_Exception(__METHOD__ . ' not supported in GitHub v3 API');
    }

    /**
     * List commits by username, repo, branch and path
     * http://develop.github.com/p/commits.html#listing_commits_for_a_file
     *
     * @param   string  $username         the username
     * @param   string  $repo             the repo
     * @param   string  $branch           the branch
     * @param   string  $path             the path
     * @throws Github_Api_Exception
     * @return  array                     list of users found
     */
    public function getFileCommits($username, $repo, $branch, $path)
    {
        throw new Github_Api_Exception(__METHOD__ . ' not supported in GitHub v3 API');
    }

    /**
     * Show a specific commit
     * http://developer.github.com/v3/git/commits/
     *
     * @param   string  $username         the username
     * @param   string  $repo             the repo
     * @param   string  $sha              the commit sha
     * @return array
     */
    public function getCommit($username, $repo, $sha)
    {
        $response = $this->get('repos/'.urlencode($username).'/'.urlencode($repo).'/git/commits/'.urlencode($sha));

        return $response;
    }

    /**
     * Create a new Commit
     * http://developer.github.com/v3/git/commits/
     *
     * @param string $username              the username
     * @param string $repo                  the repo
     * @param string $message               the commit message
     * @param string $tree                  String of the SHA of the tree object this commit points to
     * @param array $parents                Array of the SHAs of the commits that were the parents of this commit.
     * If omitted or empty, the commit will be written as a root commit. For a single parent, an array of one SHA
     * should be provided, for a merge commit, an array of more than one should be provided.
     * @param array $options                The committer section is optional and will be filled with the author
     * data if omitted. If the author section is omitted, it will be filled in with the authenticated users
     * information and the current date.
     * author.name
     *   String of the name of the author of the commit
     * author.email
     *   String of the email of the author of the commit
     * author.date
     *   Timestamp of when this commit was authored
     * committer.name
     *   String of the name of the committer of the commit
     * committer.email
     *   String of the email of the committer of the commit
     * committer.date
     *   Timestamp of when this commit was committed
     *
     * @return array
     */
    public function createCommit($username, $repo, $message, $tree, array $parents, array $options = array())
    {
        $params = array(
            'message' => $message,
            'tree' => $tree,
            'parents' => $parents
        );

        if(isset($options['author'])) {
            $params['author'] = $options['author'];
        }

        if(isset($options['committer'])) {
            $params['committer'] = $options['committer'];
        }

        $response = $this->post('repos/'.urlencode($username).'/'.urlencode($repo).'/git/commits', $params);

        return $response;

    }
}
