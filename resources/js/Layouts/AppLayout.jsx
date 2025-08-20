import { Toaster } from '@/Components/ui/toaster';
import { flashMessage } from '@/lib/utils';
import { Dialog, Transition } from '@headlessui/react';
import { Head, Link, usePage } from '@inertiajs/react';
import { Avatar, AvatarFallback } from '@radix-ui/react-avatar';
import { IconLayoutSidebar, IconX } from '@tabler/icons-react';
import { Fragment, useEffect, useState } from 'react';
import { toast } from 'sonner';
import Sidebar from './Partials/Sidebar';
import SidebarResponsive from './Partials/SidebarResponsive';

export default function AppLayout({ title, children }) {
    // State to manage sidebar visibility
    const [sidebarOpen, setSidebarOpen] = useState(false);

    const { url } = usePage();

    // Get flash message from the page props
    const flash = flashMessage(usePage());

    // Show flash message if exists
    useEffect(() => {
        if (flash && flash.message && flash.type === 'warning') {
            toast[flash.type](flash.message);
        }
    }, [flash]);

    return (
        <>
            <Head title={title} />

            <Toaster position="top-center" richColors />

            <div>
                <Transition.Root show={sidebarOpen} as={Fragment}>
                    <Dialog as="div" className="relative z-50 lg:hidden" onClose={setSidebarOpen}>
                        <Transition.Child
                            as={Fragment}
                            enter="transition-opacity ease-linear duration-300"
                            enterFrom="opacity-0"
                            enterTo="opacity-100"
                            leave="transition-opacity ease-linear duration-200"
                            leaveFrom="opacity-100"
                            leaveTo="opacity-0"
                        >
                            <div className="fixed inset-0 bg-gray-900/80" />
                        </Transition.Child>

                        <div className="fixed inset-0 flex">
                            <Transition.Child
                                as={Fragment}
                                enter="transition ease-in-out duration-300 transform"
                                enterFrom="-translate-x-full"
                                enterTo="translate-x-0"
                                leave="transition ease-in-out duration-300 transform"
                                leaveFrom="translate-x-0"
                                leaveTo="-translate-x-full"
                            >
                                <Dialog.Panel className="relative mr-16 flex w-full max-w-xs flex-1">
                                    <Transition.Child
                                        as={Fragment}
                                        enter="ease-in-out duration-300"
                                        enterFrom="opacity-0"
                                        enterTo="opacity-100"
                                        leave="ease-in-out duration-200"
                                        leaveFrom="opacity-100"
                                        leaveTo="opacity-0"
                                    >
                                        <div className="absolute left-full top-0 flex w-16 justify-center pt-5">
                                            <button
                                                type="button"
                                                className="-m-2.5 p-2.5"
                                                onClick={() => setSidebarOpen(false)}
                                            >
                                                <IconX className="size-6 text-white" />
                                            </button>
                                        </div>
                                    </Transition.Child>
                                    <div className="flex grow flex-col gap-y-5 overflow-y-auto bg-gradient-to-b from-blue-500 via-blue-600 to-blue-700 px-6 pb-2">
                                        {/* Sidebar Responsive */}
                                        <SidebarResponsive url={url} />
                                    </div>
                                </Dialog.Panel>
                            </Transition.Child>
                        </div>
                    </Dialog>
                </Transition.Root>

                <div className="hidden p-2.5 lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
                    <div className="flex grow flex-col gap-y-5 overflow-y-auto rounded-xl bg-gradient-to-b from-blue-500 via-blue-600 to-blue-700 px-4">
                        {/* Sidebar Desktop */}
                        <Sidebar url={url} />
                    </div>
                </div>

                {/* Header */}
                <div className="sticky top-0 z-40 flex items-center gap-x-6 bg-white p-4 shadow-sm sm:px-6 lg:hidden">
                    <button
                        type="button"
                        className="-m-2.5 p-2.5 text-gray-700 lg:hidden"
                        onClick={() => setSidebarOpen(true)}
                    >
                        <IconLayoutSidebar className="size-6" />
                    </button>

                    <div className="flex-1 text-sm font-semibold leading-6 text-foreground">{title}</div>

                    <Link href="#">
                        <Avatar>
                            <AvatarFallback>X</AvatarFallback>
                        </Avatar>
                    </Link>
                </div>

                <main className="py-4 lg:pl-72">
                    <div className="px-4">{children}</div>
                </main>
            </div>
        </>
    );
}
