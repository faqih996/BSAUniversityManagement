import Banner from '@/Components/Banner';
import { Card, CardContent } from '@/Components/ui/card';
import { Toaster } from '@/Components/ui/toaster';
import { flashMessage } from '@/lib/utils';
import { Head, usePage } from '@inertiajs/react';
import { useEffect } from 'react';
import { toast } from 'sonner';
import HeaderStudentLayout from './Partials/HeaderStudentLayout';

export default function StudentLayout({ title, children }) {
    const checkFee = usePage().props.checkFee;

    const { url } = usePage();

    const auth = usePage().props.auth.user;

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

            <div className="min-h-full">
                <div className="pb-32 bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700">
                    {/* Header Layout */}
                    <HeaderStudentLayout auth={auth} url={url} />
                </div>

                <main className="px-6 pb-12 -mt-32 lg:px-28">
                    <Card>
                        <CardContent className="p-6">{children}</CardContent>
                    </Card>

                    {/* Check Fee */}
                    {checkFee === false && (
                        <Banner message="Harap melakukan pembayaran uang kuliah tunggal terlebih dahulu." />
                    )}
                </main>
            </div>
        </>
    );
}
