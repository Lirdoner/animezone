<?php


namespace Anime\Controller;


use Anime\Model\Category\CategoryManager;
use Anime\Model\Comment\Comment;
use Anime\Model\Comment\CommentManager;
use Anime\Model\Episode\EpisodeManager;
use Anime\Model\News\NewsManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sequence\Controller;
use Sequence\Validator\Digits;
use Sequence\Validator\StringLength;

class CommentsController extends Controller
{
    /**
     * @var \Anime\Model\Comment\CommentManager
     */
    protected $commentManager;

    public function init()
    {
        $this->commentManager = new CommentManager($this->getDatabase());
    }

    public function listAction(Request $request)
    {
        $type = $request->request->get('type');
        $to = $request->request->get('to');
        $limit = $request->request->get('limit', 5);
        $offset = $request->request->get('offset', null);

        $validInt = new Digits();

        if(!$validInt->isValid($type) || !$validInt->isValid($to))
        {
            throw $this->createNotFoundException(sprintf('TYPE i TO muszą być typu int. Otrzymano TYPE: "%s"; TO: "%s"', $type, $to));
        }

        if((null !== $limit && !$validInt->isValid($limit)) || (null !== $offset && !$validInt->isValid($offset)))
        {
            throw $this->createNotFoundException(sprintf('Limit ("%s") lub offset ("%s") muszą być typu int.', $limit, $offset));
        }

        //comments
        $total = $this->commentManager->count(array('type' => $type, 'to' => $to));
        $list = $this->commentManager->findListBy(array('type' => $type, 'to' => $to), 'date DESC', $limit, $offset);

        return $this->render('Comments/list', array(
            'total' => $total,
            'list' => $list,
            'limit' => $limit,
        ));
    }

    public function previousAction(Request $request)
    {
        $type = $request->request->get('type');
        $to = $request->request->get('to');
        $limit = $request->request->get('limit', 0);
        $offset = $request->request->get('offset', 5);

        $validInt = new Digits();

        if(!$validInt->isValid($type) || !$validInt->isValid($to))
        {
            throw $this->createNotFoundException(sprintf('TYPE i TO muszą być typu int. Otrzymano TYPE: "%s"; TO: "%s"', $type, $to));
        }

        if((null !== $limit && !$validInt->isValid($limit)) || !$validInt->isValid($offset))
        {
            throw $this->createNotFoundException(sprintf('Limit ("%s") lub offset ("%s") muszą być typu int.', $limit, $offset));
        }

        $list = $this->commentManager->findListBy(array('type' => $type, 'to' => $to), 'date DESC', $limit, $offset);

        return $this->render('Comments/previous', array(
            'list' => $list,
        ));
    }

    public function updateAction(Request $request)
    {
        $user = $this->getUser();

        $comment = $this->commentManager->create(array(
            'id' => $request->request->get('id', null),
            'type' => $request->request->get('type'),
            'to' => $request->request->get('to'),
            'message' => htmlspecialchars($request->request->get('message')),
            'user_id' => $user->getId(),
        ));

        $validInt = new Digits();

        if(!$validInt->isValid($comment->getType()) || !$validInt->isValid($comment->getTo()))
        {
            throw $this->createNotFoundException(sprintf('TYPE i TO muszą być typu int. Otrzymano TYPE: "%s"; TO: "%s"', $comment->getType(), $comment->getTo()));
        }

        $validString = new StringLength(array('min 40'));

        if(!$validString->isValid($comment->getMessage()))
        {
            return new Response('Treść wiadomości musi zawierać więcej niż 40 znaków.');
        }

        $this->getDatabase()->beginTransaction();

        if(null !== $comment->getId())
        {
            //check if comment exists
            if(false == $data = $this->commentManager->find($comment->getId()))
            {
                throw $this->createNotFoundException(sprintf('Komentarz o podanym ID: "%s" nie istnieje.', $comment->getId()));
            }

            $comment->setDate(new \DateTime($data['date']));
            $comment->setUserId($data['user_id']);

            //update count of user comments
            //moved to trigger in mysql
            //$user->setCustomField('commented', $this->commentManager->count(array('user_id' => $user->getId())));
            //$this->get('user_manager')->updateUser($user);
        }

        $this->commentManager->update($comment);

        $criteria = null !== $comment->getId() ? $comment->getId() : $this->getDatabase()->lastInsertId();

        $this->getDatabase()->commit();

        $list = $this->commentManager->findListBy('k.id='.$criteria, null, 1);

        return $this->render('Comments/previous', array(
            'list' => $list,
        ));
    }

    public function editAction(Request $request)
    {
        $id = $request->request->get('id');

        $validInt = new Digits();

        if(!$validInt->isValid($id))
        {
            throw $this->createNotFoundException(sprintf('ID ("%s") musi być typu int.', $id));
        }

        //check if comment exists
        if(false == $comment = $this->commentManager->findOneBy(array('id' => $id)))
        {
            throw $this->createNotFoundException(sprintf('Komentarz o podanym ID: "%s" nie istnieje.', $id));
        }

        return $this->render('Comments/edit', array(
            'comment' => $comment,
        ));
    }

    public function deleteAction(Request $request)
    {
        $user = $this->getUser();

        $id = $request->request->get('id');

        $validInt = new Digits();

        if(!$validInt->isValid($id))
        {
            throw $this->createNotFoundException(sprintf('ID ("%s") musi być typu int.', $id));
        }

        //check if comment exists
        if(false == $comment = $this->commentManager->findOneBy(array('id' => $id)))
        {
            throw $this->createNotFoundException(sprintf('Komentarz o podanym ID: "%s" nie istnieje.', $id));
        }

        $comment = $this->commentManager->create($comment);

        //check if user have rights to delete an comment
        if($comment->getUserId() !== $user->getId() && !$user->isAdmin())
        {
            $msg = sprintf('Brak uprawnień. Wymagany user_id="%s" lub uprawnienia administratora, otrzymano user_id: "%s"', $comment->getUserId(), $user->getId());

            $this->get('logger')->error($msg);

            throw $this->createNotFoundException($msg);
        }

        $this->getDatabase()->beginTransaction();

        $this->commentManager->delete($comment);

        //moved to trigger in mysql
        //$user->setCustomField('commented', $user->getCustomField('commented') - 1);
        //$this->get('user_manager')->updateUser($user);

        //update count of user comments
        //$user->setCustomField('commented', $this->commentManager->count(array('user_id' => $user->getId())));
        //$this->get('user_manager')->updateUser($user);

        $this->getDatabase()->commit();

        return new Response();
    }

    public function redirectAction($commentID)
    {
        //check if comment exists
        if(false == $comment = $this->commentManager->findOneBy(array('id' => $commentID)))
        {
            throw $this->createNotFoundException(sprintf('Komentarz o podanym ID: "%s" nie istnieje.', $commentID));
        }

        $comment = $this->commentManager->create($comment);
        $url = null;

        if(Comment::TYPE_CATEGORY == $comment->getType() || Comment::TYPE_EPISODE == $comment->getType())
        {
            $categories = new CategoryManager($this->getDatabase());

            if(Comment::TYPE_EPISODE == $comment->getType())
            {
                $episodes = new EpisodeManager($this->getDatabase());

                // check if episode exists, if not, delete comment
                if(false == $data = $episodes->find($comment->getTo()))
                {
                    $this->commentManager->delete($comment);

                    throw $this->createNotFoundException(sprintf('Episode %s does not exists.', $comment->getTo()));
                }

                $episode = $episodes->create($data);
                $category = $categories->create($categories->find($episode->getCategoryId()));

                $url = $this->generateUrl('episodes_show', array('cat' => $category->getAlias(), 'id' => $episode->getNumber()));
            } else
            {
                // check if category exists, if not, delete comment
                if(false == $data = $categories->find($comment->getTo()))
                {
                    $this->commentManager->delete($comment);

                    throw $this->createNotFoundException(sprintf('Category %s does not exists.', $comment->getTo()));
                }

                $category = $categories->create($data);

                $url = $this->generateUrl('episodes_cat', array('cat' => $category->getAlias()));
            }
        } elseif(Comment::TYPE_NEWS == $comment->getType())
        {
            $newsManager = new NewsManager($this->getDatabase());

            // check if news exists, if not, delete comment
            if(false == $data = $newsManager->find($comment->getTo()))
            {
                $this->commentManager->delete($comment);

                throw $this->createNotFoundException(sprintf('News %s does not exists.', $comment->getTo()));
            }

            $news = $newsManager->create($data);

            $url = $this->generateUrl('news_show', array('slug' => $news->getAlias()));
        }

        return $this->redirect($url);
    }
} 