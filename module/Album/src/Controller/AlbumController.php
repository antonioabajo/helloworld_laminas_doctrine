<?php
namespace Album\Controller;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Album\Form\AlbumForm;
use Album\Entity\Album;
use Doctrine\ORM\EntityManager;
class AlbumController extends AbstractActionController
{
    
    private $entityManager;
    
    public function __construct(EntityManager $entityManager) {
        $this->entityManager=$entityManager;
    }
    
    public function indexAction()
    {
        //$dql = "SELECT a FROM Album a";
        //$query = $this->entityManager->createQuery($dql);
        //$albums = $query->getArrayResult();
        $albumRepository = $this->entityManager->getRepository(Album::class);
        $albums= $albumRepository->findAll();
        return new ViewModel(['albums' => $albums,]);
    }
    
    public function addAction()
    {
        $form = new AlbumForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $album = new Album();
        $form->setInputFilter($album->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return ['form' => $form];
        }

        $album->exchangeArray($form->getData());
        $this->entityManager->persist($album);
        // Apply changes to database.
        $this->entityManager->flush();
        return $this->redirect()->toRoute('album');
    }
    
    // module/Album/src/Controller/AlbumController.php:
// ...

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('album', ['action' => 'add']);
        }

        // Retrieve the album with the specified id. Doing so raises
        // an exception if the album is not found, which should result
        // in redirecting to the landing page.
        try {
            $albumRepository = $this->entityManager->getRepository(Album::class);
            $album= $albumRepository->findOneById($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('album', ['action' => 'index']);
        }

        $form = new AlbumForm();
        $form->bind($album);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($album->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }

        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
        }

        // Redirect to album list
        return $this->redirect()->toRoute('album', ['action' => 'index']);
    }
    
    public function deleteAction()
    {
         $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('album');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $albumRepository = $this->entityManager->getRepository(Album::class);
                $album = $albumRepository->findOneById($id);
                if($album == null){
                  return $this->redirect()->toRoute('album');
                }
                 $this->entityManager->remove($album);
                 $this->entityManager->flush();
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('album');
        }

        return [
            'id'    => $id,
            'album' => $this->entityManager->getRepository(Album::class)->findOneById($id),
        ];
    }
}