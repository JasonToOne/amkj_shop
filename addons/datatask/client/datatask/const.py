import os
import sys
class _const:
    class ConstError(TypeError):pass
    def __setattr__(self,name,value):
        if name in self.__dict__:
            raise self.ConstError("Can't rebind const (%s)" %name)
        self.__dict__[name]=value
const = _const()
const.ROOT_PATH = os.path.split(os.path.realpath(sys.argv[0]))[0]
sys.modules[__name__] = const