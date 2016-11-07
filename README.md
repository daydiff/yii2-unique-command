# Yii2 command behavior allows you to control uniqueness of a command

## How to use

```php
use Daydiff\UniqueCommand\UniqueCommandBehavior;
use yii\console\Controller;

class UniqueController extends Controller
{

    public function behaviors()
    {
        return [
            [
                'class' => UniqueCommandBehavior::className(),
                'actions' => ['foo'] //an action foo will be unique
            ]
        ];
    }

    /**
     * Unique action
     */
    public function actionFoo()
    {
        //just if it do really long work
        sleep(5);
        return 'unique';
    }

    /**
     * Non unique action
     */
    public function actionBar()
    {
        //just if it do really long work
        sleep(5);
        return 'non-unique';
    }
}
```