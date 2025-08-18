import NavLink from "@/Components/NavLink";
import { Avatar, AvatarFallback } from "@/Components/ui/avatar";
import { Link } from "@inertiajs/react";
import { IconBuildingSkyscraper, IconCalendarTime, IconCircleKey, IconDoor, IconLayout2, IconSchool } from "@tabler/icons-react";


export default function Sidebar({url}){
    return (

        <nav className="flex flex-col flex-1">

            <ul role="list" className="flex flex-col flex-1">

                <li className="-mx-6">

                    <Link
                        href='#'
                        className="flex items-center px-6 py-3 text-sm font-semibold leading-6 text-white gap-x-4 hover:bg-blue-800"
                    >

                        <Avatar>

                            <AvatarFallback>X</AvatarFallback>

                        </Avatar>

                        <div className="flex flex-col text-left">
                            <span className="font-bold truncate">Monkey D Luffy</span>
                            <span className="truncate">Admin</span>
                        </div>

                    </Link>

                </li>

                <NavLink url='#' active={url.startsWith('/admin/dashboard')} title='Dashboard' icon={IconLayout2} />

                <div className="px-3 py-2 text-xs font-medium text-white"></div>

                <NavLink url='#' active={url.startsWith('/admin/faculties')} title='Fakultas' icon={IconBuildingSkyscraper} />
                <NavLink url='#' active={url.startsWith('/admin/departments')} title='Program Studi' icon={IconSchool} />
                <NavLink url='#' active={url.startsWith('/admin/academic-years')} title='Tahun Ajaran' icon={IconCalendarTime} />
                <NavLink url='#' active={url.startsWith('/admin/classrooms')} title='Kelas' icon={IconDoor} />
                <NavLink url='#' active={url.startsWith('/admin/roles')} title='Peran' icon={IconCircleKey} />

            </ul>

        </nav>

    )
}
