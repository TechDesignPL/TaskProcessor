<?php

namespace TechDesign\TaskProcessor;

if (class_exists('Thread')) {
	class ThreadProxy extends \Thread {}
} else {
	class ThreadProxy{}
}
