<?php

namespace PHPCensor\Model\Build;

/**
 * Gitlab Build Model
 *
 * @author André Cianfarani <a.cianfarani@c2is.fr>
 */
class GitlabBuild extends GitBuild
{

    /**
    * Get link to commit from another source (i.e. Github)
    */
    public function getCommitLink()
    {
        $domain = $this->getProject()->getAccessInformation("domain");
        return 'http://' . $domain . '/' . $this->getProject()->getReference() . '/commit/' . $this->getCommitId();
    }

    /**
    * Get link to branch from another source (i.e. Github)
    */
    public function getBranchLink()
    {
        $domain = $this->getProject()->getAccessInformation("domain");
        return 'http://' . $domain . '/' . $this->getProject()->getReference() . '/tree/' . $this->getBranch();
    }

    /**
     * Get link to specific file (and line) in a the repo's branch
     */
    public function getFileLinkTemplate()
    {
        return sprintf(
            'http://%s/%s/blob/%s/{FILE}#L{LINE}',
            $this->getProject()->getAccessInformation("domain"),
            $this->getProject()->getReference(),
            $this->getCommitId()
        );
    }

    /**
    * Get the URL to be used to clone this remote repository.
    */
    protected function getCloneUrl()
    {
        $key = trim($this->getProject()->getSshPrivateKey());

        if (!empty($key)) {
            $user = $this->getProject()->getAccessInformation("user");
            $domain = $this->getProject()->getAccessInformation("domain");
            $port = $this->getProject()->getAccessInformation('port');

            $url = $user . '@' . $domain . ':';

            if (!empty($port)) {
                $url .= $port . '/';
            }

            $url .= $this->getProject()->getReference() . '.git';

            return $url;
        }
    }
}
