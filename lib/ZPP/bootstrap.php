<?php

namespace ZPP;


ZPP::registerType("l", new Handler\Long());
ZPP::registerType("d", new Handler\Double());
ZPP::registerType("s", new Handler\String());
ZPP::registerType("p", new Handler\String(true));
ZPP::registerType("b", new Handler\Boolean());
ZPP::registerType("r", new Handler\Resource());
ZPP::registerType("a", new Handler\ArrayHandler());
ZPP::registerType("A", new Handler\ArrayHandler(true));
ZPP::registerType("i", new Handler\Iterable());
ZPP::registerType("z", new Handler\Variable());
ZPP::registerType("f", new Handler\Callback());
ZPP::registerType("o", new Handler\Object());
ZPP::registerType("O", new Handler\Object(true));
ZPP::registerType("c", new Handler\ClassHandler());
ZPP::registerType("C", new Handler\ClassHandler(true));

