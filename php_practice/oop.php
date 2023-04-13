<?php 
    // echo "this is class practice"

    class person{
        public $name;
        public $age;
        function __construct($name, $age) {
            $this->name = $name;
            $this->age = $age;
        }

        public function intro(){
            return "Greetings {$this->name} !";
        }

        public function basic_info(){
            return "{$this->name} is {$this->age} year old.";
        }

    }

    class student extends person{
        public $grade;
        public function __construct($name, $age, $grade) {
            $this->name = $name;
            $this->age = $age;
            $this->grade = $grade;
        }
        public function student_intro(){
            return "{$this->name} is from {$this->grade}th grade and is {$this->age} year old.";
        }
        public function basic_info(){
            return "{$this->name} is from {$this->grade}th grade.";
        }
    }

    $s1 = new student("Rajesh", "12", "7");
    echo $s1->student_intro();
    echo "<br>";
    echo $s1->basic_info();
    // echo "<br>";
    // echo $s1->b

?>