/**
* NOTE!!! This class is vestigial and not in use.
*/

<?php namespace Elemental\Generators;


use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\Application;



class CollectionSeedGenerator {

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;




    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * Create a new seed file at the given path.
     *
     * @param  array  $attributes
     * @return string
     */
    public function create($nickname, $slug, $type, $attributes)
    {   

        $filename = studly_case($nickname).'CollectionSeeder';
        $path = __DIR__.'/../../../database/seeds/'.$filename.'.php';

        $stub = $this->getStub();
        $seed = $this->populateStub($stub, $nickname, $slug, $type, $attributes);
        $this->files->put($path, $seed);

        return $filename;
    }


    /**
     * Populate the place-holders in the migration stub.
     *
     * @param  string  $name
     * @param  string  $stub
     * @param  string  $table
     * @return string
     */
    protected function populateStub($stub, $nickname, $slug, $type, $attributes)
    {
        $seedClass = studly_case($nickname);
        $stub = str_replace('{{seed_name}}', $seedClass, $stub);
        $stub = str_replace('{{nickname}}', $nickname, $stub);
        $stub = str_replace('{{slug}}', $slug, $stub);
        $stub = str_replace('{{type}}', $type, $stub);

        $attributeString = '';
        foreach($attributes as $key => $val) {
            $attributeString .= "\t\t\$attribute = new CollectionAttribute;\n";
            $attributeString .= "\t\t\$attribute->key = '".$key."';\n";
            $attributeString .= "\t\t\$attribute->value = '".$val."';\n";
            $attributeString .= "\t\t\$attribute->save();\n";
            $attributeString .= "\t\t\$collection->attributes()->save(\$attribute);\n";
        }

        $stub = str_replace('{{attributes}}', $attributeString, $stub);
        return $stub;
    }


    /**
     * Get the migration stub file.
     *
     * @param  string  $table
     * @param  bool    $create
     * @return string
     */
    protected function getStub()
    {
        return $this->files->get($this->getStubPath().'/collection.stub');
    }

    /**
     * Get the path to the stubs.
     *
     * @return string
     */
    public function getStubPath()
    {
        return __DIR__.'/stubs';
    }

}
